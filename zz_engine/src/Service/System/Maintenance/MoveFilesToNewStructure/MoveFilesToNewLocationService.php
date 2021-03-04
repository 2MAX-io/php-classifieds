<?php

declare(strict_types=1);

namespace App\Service\System\Maintenance\MoveFilesToNewStructure;

use App\Entity\Listing;
use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Helper\ListingFileHelper;
use App\Service\System\Maintenance\MoveFilesToNewStructure\Dto\MoveFilesToNewLocationDto;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SlevomatCodingStandard\Helpers\StringHelper;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class MoveFilesToNewLocationService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Filesystem $filesystem, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    public function moveToNewLocation(MoveFilesToNewLocationDto $moveFilesToNewLocationDto): void
    {
        $qb = $this->em->createQueryBuilder();
        $qb->addSelect('listing');
        $qb->addSelect('listingFile');
        $qb->from(Listing::class, 'listing');
        $qb->join('listing.listingFiles', 'listingFile');

        $qb->andWhere($qb->expr()->eq('listingFile.fileDeleted', 0));
        $qb->andWhere($qb->expr()->like('listingFile.path', ':pathContainsLike')); // old files path
        $qb->setParameter(':pathContainsLike', '%0000_legacy%');

        $qb->addOrderBy('listing.id', Criteria::ASC);

        if ($moveFilesToNewLocationDto->getLimit()) {
            $qb->setMaxResults($moveFilesToNewLocationDto->getLimit());
        }

        /** @var Listing[] $listingList */
        $listingList = $qb->getQuery()->getResult();
        foreach ($listingList as $listing) {
            foreach ($listing->getListingFilesAll() as $listingFile) {
                $oldFilePath = Path::makeAbsolute($listingFile->getPath(), FilePath::getPublicDir());

                if (!\file_exists($oldFilePath)) {
                    $this->logger->error('[MoveFilesToNewLocationService] file does not exists, from listing id: {listingId}, valid until: {validUntil}, path: {path}', [
                        'path' => $oldFilePath,
                        'listingId' => $listing->getId(),
                        'validUntil' => $listing->getValidUntilDateStringOrNull(),
                    ]);

                    continue;
                }

                if (!StringHelper::startsWith($oldFilePath, FilePath::getListingFilePath())) {
                    $this->logger->debug('[MoveFilesToNewLocationService] file not in listing file path', [
                        'path' => $oldFilePath,
                        'listingId' => $listing->getId(),
                        'validUntil' => $listing->getValidUntilDateStringOrNull(),
                    ]);

                    continue;
                }
                $this->logger->debug('[MoveFilesToNewLocationService] generating target path for listing id: {listingId}, valid until: {validUntil}, path: {path}', [
                    'path' => $oldFilePath,
                    'listingId' => $listing->getId(),
                    'validUntil' => $listing->getValidUntilDateStringOrNull(),
                ]);
                $newFilePath = ListingFileHelper::getDestinationDirectory($listing).\DIRECTORY_SEPARATOR.\basename($oldFilePath);
                FileHelper::throwExceptionIfPathOutsideDir($newFilePath, FilePath::getListingFilePath());
                FileHelper::throwExceptionIfUnsafeFilename($newFilePath);

                if ($moveFilesToNewLocationDto->getPerformMove()) {
                    $this->logger->info('[MoveFilesToNewLocationService] moving files from listing id: {listingId}, valid until: {validUntil}, path: {oldFilePath} to: {newFilePath}', [
                        'oldFilePath' => $oldFilePath,
                        'newFilePath' => $newFilePath,
                        'listingId' => $listing->getId(),
                        'validUntil' => $listing->getValidUntilDateStringOrNull(),
                    ]);

                    $this->filesystem->copy($oldFilePath, $newFilePath);

                    $listingFile->setPath(Path::makeRelative($newFilePath, FilePath::getPublicDir()));

                    $this->em->persist($listingFile);
                    $this->em->flush();
                } else {
                    $this->logger->info('[MoveFilesToNewLocationService] dry run, real run would move files from listing id: {listingId}, valid until: {validUntil}, path: {oldFilePath} to: {newFilePath}', [
                        'oldFilePath' => $oldFilePath,
                        'newFilePath' => $newFilePath,
                        'listingId' => $listing->getId(),
                        'validUntil' => $listing->getValidUntilDateStringOrNull(),
                    ]);
                }
            }
        }
    }
}
