<?php

declare(strict_types=1);

namespace App\Service\Cron\DeleteOldListingFiles;

use App\Entity\Listing;
use App\Helper\FilePath;
use App\Service\Cron\DeleteOldListingFiles\Dto\DeleteOldListingFilesDto;
use Carbon\Carbon;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SlevomatCodingStandard\Helpers\StringHelper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Webmozart\PathUtil\Path;

class DeleteOldListingFilesService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->filesystem = $filesystem;
    }

    public function removeListingFilesOlderThan(DeleteOldListingFilesDto $deleteOldListingFilesDto): void
    {
        if ($deleteOldListingFilesDto->getDeleteOlderThanInDays() < 1) {
            throw new \UnexpectedValueException('number of days must be greater than 0');
        }

        $qb = $this->em->createQueryBuilder();
        $qb->addSelect('listing');
        $qb->addSelect('listingFile');
        $qb->from(Listing::class, 'listing');
        $qb->join('listing.listingFiles', 'listingFile');

        $qb->andWhere($qb->expr()->lt('listing.validUntilDate', ':removeBeforeDate'));
        $qb->setParameter(':removeBeforeDate', Carbon::now()->subDays($deleteOldListingFilesDto->getDeleteOlderThanInDays())->format('Y-m-d H:i:s'));

        $qb->andWhere($qb->expr()->eq('listing.userDeactivated', 1));

        $qb->addOrderBy('listing.validUntilDate', Criteria::ASC);

        if ($deleteOldListingFilesDto->getLimit()) {
            $qb->setMaxResults($deleteOldListingFilesDto->getLimit());
        }

        /** @var Listing[] $listingList */
        $listingList = $qb->getQuery()->getResult();
        foreach ($listingList as $listing) {
            foreach ($listing->getListingFiles() as $listingFile) {
                $fileAbsolutePath = Path::makeAbsolute($listingFile->getPath(), FilePath::getPublicDir());

                if (!\file_exists($fileAbsolutePath)) {
                    $this->logger->error('[RemoveOldListingFilesService] file does not exists, from listing id: {listingId}, valid until: {validUntil}, path: {path}', [
                        'path' => $fileAbsolutePath,
                        'listingId' => $listing->getId(),
                        'validUntil' => $listing->getValidUntilDate()->format('Y-m-d H:i:s'),
                    ]);

                    continue;
                }

                if (!StringHelper::startsWith($fileAbsolutePath, FilePath::getListingFilePath())) {
                    continue;
                }

                if ($deleteOldListingFilesDto->getPerformFileDeletion()) {
                    $this->logger->info('[RemoveOldListingFilesService] deleting files from listing id: {listingId}, valid until: {validUntil}, path: {path}', [
                        'path' => $fileAbsolutePath,
                        'listingId' => $listing->getId(),
                        'validUntil' => $listing->getValidUntilDate()->format('Y-m-d H:i:s'),
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
                }


                if (!$deleteOldListingFilesDto->getPerformFileDeletion()) {
                    $this->logger->debug('[RemoveOldListingFilesService] dry run, real run would delete files from listing id: {listingId}, valid until: {validUntil}, path: {path}', [
                        'path' => $fileAbsolutePath,
                        'listingId' => $listing->getId(),
                        'validUntil' => $listing->getValidUntilDate()->format('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        $this->em->flush();
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
            $this->logger->error('path does not contain expected string', [
                'path' => $path,
                'mustContain' => $mustContain,
            ]);

            throw new \UnexpectedValueException('path does not contain expected string');
        }

        $finder = new Finder();
        if (0 === $finder->in($parentDirectory)->count()) {
            $this->filesystem->remove($parentDirectory);

            $this->logger->debug('removed parent directory', [
                'parentDirectory' => $parentDirectory,
            ]);
        } else {
            $this->logger->info('parent directory is not empty: {parentDirectory}', [
                'parentDirectory' => $parentDirectory,
            ]);
        }
    }
}
