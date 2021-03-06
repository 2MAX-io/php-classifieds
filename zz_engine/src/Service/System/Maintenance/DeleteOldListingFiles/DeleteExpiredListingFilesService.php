<?php

declare(strict_types=1);

namespace App\Service\System\Maintenance\DeleteOldListingFiles;

use App\Entity\Listing;
use App\Helper\DateHelper;
use App\Helper\FilePath;
use App\Helper\StringHelper;
use App\Service\System\Maintenance\DeleteOldListingFiles\Dto\DeleteExpiredListingFilesDto;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Webmozart\PathUtil\Path;

class DeleteExpiredListingFilesService
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Filesystem $filesystem, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->filesystem = $filesystem;
    }

    public function deleteExpiredListingFiles(DeleteExpiredListingFilesDto $deleteExpiredListingFilesDto): void
    {
        if ($deleteExpiredListingFilesDto->getDaysOldToDelete() < 1) {
            throw new \UnexpectedValueException('number of days must be greater than 0');
        }
        $deleteBeforeDate = DateHelper::carbonNow()->subDays($deleteExpiredListingFilesDto->getDaysOldToDelete());

        $qb = $this->em->createQueryBuilder();
        $qb->addSelect('listing');
        $qb->addSelect('listingFile');
        $qb->from(Listing::class, 'listing');
        $qb->join('listing.listingFiles', 'listingFile');

        $qb->andWhere($qb->expr()->lt('listing.expirationDate', ':deleteBeforeDate'));
        $qb->setParameter(':deleteBeforeDate', $deleteBeforeDate->format(DateHelper::MYSQL_FORMAT));

        $qb->andWhere($qb->expr()->eq('listingFile.fileDeleted', 0));
        $qb->andWhere($qb->expr()->eq('listing.userDeactivated', 1));

        $qb->addOrderBy('listing.expirationDate', Criteria::ASC);

        if ($deleteExpiredListingFilesDto->getLimit()) {
            $qb->setMaxResults($deleteExpiredListingFilesDto->getLimit());
        }
        $query = $qb->getQuery();

        $executedCount = 0;
        while ($listingList = $this->iterate($query, $executedCount, 50)) {
            foreach ($listingList as $listing) {
                if ($deleteExpiredListingFilesDto->getLimit() && $executedCount >= $deleteExpiredListingFilesDto->getLimit()) {
                    break 2;
                }
                ++$executedCount;
                foreach ($listing->getListingFilesAll() as $listingFile) {
                    $fileAbsolutePath = Path::makeAbsolute($listingFile->getPath(), FilePath::getPublicDir());

                    if (!\file_exists($fileAbsolutePath)) {
                        $this->logger->error('[DeleteExpiredListingFilesService] file does not exists, from listing id: {listingId}, expiration date: {expirationDate}, path: {path}', [
                            'path' => $fileAbsolutePath,
                            'listingId' => $listing->getId(),
                            'expirationDate' => $listing->getExpirationDateStringOrNull(),
                        ]);
                    }

                    if (!StringHelper::startsWith($fileAbsolutePath, FilePath::getListingFilePath())) {
                        $this->logger->error('[DeleteExpiredListingFilesService] listing file path outside of expected, skipping: {path}', [
                            'path' => $fileAbsolutePath,
                            'listingId' => $listing->getId(),
                            'expirationDate' => $listing->getExpirationDateStringOrNull(),
                        ]);

                        continue;
                    }

                    if ($deleteExpiredListingFilesDto->getPerformFileDeletion()) {
                        $this->logger->info('[DeleteExpiredListingFilesService] deleting files from listing id: {listingId}, expiration date: {expirationDate}, path: {path}', [
                            'path' => $fileAbsolutePath,
                            'listingId' => $listing->getId(),
                            'expirationDate' => $listing->getExpirationDateStringOrNull(),
                        ]);

                        $this->filesystem->remove($fileAbsolutePath);
                        $this->filesystem->remove($listingFile->getPathInListSize());
                        $this->filesystem->remove($listingFile->getPathInNormalSize());

                        $this->removeParentDirectory($fileAbsolutePath, 1, 'listing_');
                        $this->removeParentDirectory($listingFile->getPathInListSize(), 1, 'listing_');
                        $this->removeParentDirectory($listingFile->getPathInNormalSize(), 1, 'listing_');

                        $this->removeParentDirectory($fileAbsolutePath, 2, 'user_');
                        $this->removeParentDirectory($listingFile->getPathInListSize(), 2, 'user_');
                        $this->removeParentDirectory($listingFile->getPathInNormalSize(), 2, 'user_');

                        $listingFile->setFileDeleted(true);
                        $listingFile->setUserRemoved(true);
                        $this->em->persist($listingFile);

                        $listing->setMainImage(null);
                        $this->em->persist($listing);
                    }

                    if (!$deleteExpiredListingFilesDto->getPerformFileDeletion()) {
                        $this->logger->info('[DeleteExpiredListingFilesService] DRY RUN, would delete from: listing id: {listingId}, expiration date: {expirationDate}, path: {path}', [
                            'path' => $fileAbsolutePath,
                            'listingId' => $listing->getId(),
                            'expirationDate' => $listing->getExpirationDateStringOrNull(),
                        ]);
                    }
                }
            }

            $this->em->flush();
            $this->em->clear();
            \gc_collect_cycles();
        }

        $this->em->flush();
        $this->em->clear();
    }

    private function removeParentDirectory(string $path, int $levels = 1, string $mustContain = null): void
    {
        $parentDirectory = \dirname($path, $levels);
        if (!\file_exists($parentDirectory)) {
            $this->logger->debug('parent directory does not exist', [
                'parentDirectory' => $parentDirectory,
            ]);

            return;
        }

        if ($mustContain && !\str_contains(\basename($parentDirectory), $mustContain)) {
            $this->logger->warning('path does not contain expected string', [
                'path' => $path,
                'mustContain' => $mustContain,
            ]);

            return;
        }

        $finder = new Finder();
        if (0 === $finder->in($parentDirectory)->count()) {
            $this->filesystem->remove($parentDirectory);

            $this->logger->debug('removed parent directory', [
                'parentDirectory' => $parentDirectory,
            ]);
        } else {
            $this->logger->debug('parent directory is not empty: {parentDirectory}', [
                'parentDirectory' => $parentDirectory,
            ]);
        }
    }

    /**
     * @return Listing[]
     */
    private function iterate(Query $query, int $offset, int $limit): array
    {
        $query->setFirstResult($offset);
        if (!$query->getMaxResults()) {
            $query->setMaxResults($limit);
        }

        return $query->getResult();
    }
}
