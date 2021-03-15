<?php

declare(strict_types=1);

namespace App\Service\System\Dev\GenerateTestData;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\CustomFieldOption;
use App\Entity\Listing;
use App\Entity\ListingCustomFieldValue;
use App\Entity\ListingFile;
use App\Entity\User;
use App\Enum\SortConfig;
use App\Form\Type\PriceForType;
use App\Helper\ArrayHelper;
use App\Helper\DateHelper;
use App\Helper\RandomHelper;
use App\Helper\SlugHelper;
use App\Repository\CategoryRepository;
use App\Service\System\Cache\RuntimeCacheService;
use App\Service\User\Account\CreateUserService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DevGenerateTestListings
{
    /**
     * @var CreateUserService
     */
    private $createUserService;

    /**
     * @var RuntimeCacheService
     */
    private $runtimeCache;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var OutputInterface|null
     */
    private $outputInterface;

    public function __construct(
        CreateUserService $createUserService,
        RuntimeCacheService $runtimeCache,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->createUserService = $createUserService;
        $this->runtimeCache = $runtimeCache;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
        $this->logger = $logger;
    }

    public function setOutputInterface(OutputInterface $outputInterface): void
    {
        $this->outputInterface = $outputInterface;
    }

    public function generate(int $count): void
    {
        $this->improvePerformance();

        $faker = Factory::create();
        $currentDate = DateHelper::carbonNow()->seconds(0);

        $persistCount = 0;
        for ($i = 0; $i < $count; ++$i) {
            $listing = new Listing();
            $listing->setTitle($faker->text(30));
            $listing->setDescription($faker->sentence(36));
            $listing->setCity($faker->city);
            $listing->setUser($this->getUser());
            $listing->setCategory($this->getRandomCategory());
            $listing->setEmailShow($this->randomBool(80));
            $listing->setFirstCreatedDate($currentDate);
            $listing->setLastEditDate($currentDate);
            $listing->setOrderByDate($currentDate);
            $listing->setEmail('user@example.com');
            $listing->setPhone('12345555555');
            $listing->setSlug(SlugHelper::getSlug($listing->getTitle()));
            $listing->setSearchText($listing->getTitle().' '.$listing->getDescription());
            $listing->setValidUntilDate(DateHelper::carbonNow()->addDays(7));

            if ($this->randomBool(15)) {
                $listing->setFeatured(true);
                $listing->setFeaturedUntilDate($listing->getValidUntilDate());
            }

            if ($this->randomBool(80)) {
                $listing->setPrice(\random_int(500, 500000) / 100);
                if ($this->randomBool(15)) {
                    $listing->setPrice(\random_int(360000, 3600000) / 100);
                }
                if ($this->randomBool(5)) {
                    $listing->setPrice(\random_int(3600000, 36000000) / 100);
                }
                if ($this->randomBool(30)) {
                    $listing->setPriceNegotiable(RandomHelper::bool());
                }
                if ($this->randomBool(30)) {
                    $listing->setPriceFor(RandomHelper::fromArray(PriceForType::getPricePerChoices()));
                }
            }

            if ($this->randomBool(60)) {
                $listing->setLocationLatitude(RandomHelper::float(49, 54, 6));
                $listing->setLocationLongitude(RandomHelper::float(14, 24, 6));
            }

            $this->setCustomFields($listing);
            $this->em->persist($listing);

            $this->addFiles($listing);

            if ($persistCount++ > 1000) {
                $this->em->flush();
                $this->em->clear();
                \gc_collect_cycles();

                $persistCount = 0;
            }
        }
        $this->em->flush();
    }

    private function setCustomFields(Listing $listing): void
    {
        foreach ($listing->getCategoryNotNull()->getCustomFields() as $customField) {
            $this->addMultipleCustomFieldValues($listing, $customField);
            $this->setYearRange($listing, $customField);
            $this->setIntegerRange($listing, $customField);
        }
    }

    private function addMultipleCustomFieldValues(Listing $listing, CustomField $customField): void
    {
        if (!$customField->getCustomFieldOptions()->count()) {
            return;
        }

        $used = [];
        $requestedNumber = 1;
        if (CustomField::CHECKBOX_MULTIPLE === $customField->getType()) {
            $requestedNumber = \random_int(1, $customField->getCustomFieldOptions()->count());
        }

        $foundCount = 0;
        $retryCount = 0;
        while ($foundCount < $requestedNumber && $retryCount < 50) {
            $listingCustomFieldValue = $this->addCustomFieldValue($listing, $customField);
            $removeDuplicate = ArrayHelper::inArray($listingCustomFieldValue->getValue(), $used);
            if ($removeDuplicate) {
                $listing->removeListingCustomFieldValue($listingCustomFieldValue);
                $this->em->remove($listingCustomFieldValue);
                ++$retryCount;

                continue;
            }
            $used[] = $listingCustomFieldValue->getValue();
            ++$foundCount;
        }
    }

    private function addCustomFieldValue(Listing $listing, CustomField $customField): ListingCustomFieldValue
    {
        /** @var CustomFieldOption $randomOption */
        $randomOption = RandomHelper::fromArray($customField->getCustomFieldOptions()->toArray());

        $customFieldValue = new ListingCustomFieldValue();
        $customFieldValue->setCustomField($customField);
        $customFieldValue->setListing($listing);
        $customFieldValue->setCustomFieldOption($randomOption);
        $customFieldValue->setValue($randomOption->getValue());
        $this->em->persist($customFieldValue);

        $listing->addListingCustomFieldValue($customFieldValue);

        return $customFieldValue;
    }

    private function setYearRange(Listing $listing, CustomField $customField): void
    {
        if (CustomField::YEAR_RANGE !== $customField->getType()) {
            return;
        }

        $optional = !$customField->getRequired();
        if ($optional && $this->randomBool(10)) {
            return; // not set optional
        }

        $customFieldValue = new ListingCustomFieldValue();
        $customFieldValue->setCustomField($customField);
        $customFieldValue->setListing($listing);
        $customFieldValue->setValue((string) \random_int(1980, (int) DateHelper::date('Y')));
        $this->em->persist($customFieldValue);
    }

    private function setIntegerRange(Listing $listing, CustomField $customField): void
    {
        if (CustomField::INTEGER_RANGE !== $customField->getType()) {
            return;
        }

        $optional = !$customField->getRequired();
        if ($optional && $this->randomBool(10)) {
            return; // not set optional
        }

        $customFieldValue = new ListingCustomFieldValue();
        $customFieldValue->setCustomField($customField);
        $customFieldValue->setListing($listing);
        $customFieldValue->setValue((string) \random_int(1, 300000));
        $this->em->persist($customFieldValue);
    }

    private function getRandomCategory(): Category
    {
        $categoryList = $this->runtimeCache->get('devGenerateListingsCategories', function () {
            return $this->categoryRepository->findBy(['lvl' => 2]);
        });

        $category = RandomHelper::fromArray($categoryList);
        /** @var Category $category */
        $category = $this->em->getReference(Category::class, $category->getId());

        return $category;
    }

    private function addFiles(Listing $listing): void
    {
        $hasImages = $this->randomBool(90);
        if (!$hasImages) {
            return;
        }
        $count = \random_int(1, 3);

        $sort = SortConfig::FIRST_VALUE;
        for ($i = 0; $i < $count; ++$i) {
            ++$sort;
            $listingFile = new ListingFile();
            $listingFile->setPath(
                RandomHelper::fromArray(
                    [
                        'static/system/1920x1080.png',
                        'static/system/1080x1920.png',
                        'static/system/category/cars2.jpg',
                        'static/system/category/electronics.jpg',
                        'static/system/category/electronics_home_appliances.jpg',
                        'static/system/category/events_wedding.jpg',
                        'static/system/category/fashion_clothing_footwear.jpg',
                        'static/system/category/finance_accounting.jpg',
                        'static/system/category/for_free.jpg',
                        'static/system/category/health_beauty_cosmetic.jpg',
                        'static/system/category/home_furniture_garden.jpg',
                        'static/system/category/jobs.jpg',
                        'static/system/category/phones_accessories.jpg',
                        'static/system/category/real_estates_houses_flats_apartments.jpg',
                        'static/system/category/renovation_construction.jpg',
                        'static/system/category/services_companies.jpg',
                        'static/system/category/sport.jpg',
                        'static/system/category/various_classifieds.jpg',
                    ]
                )
            );
            $listingFile->setSort($sort);
            $listingFile->setSizeBytes(363636);
            $listingFile->setMimeType('image/jpeg');
            $listingFile->setUserOriginalFilename(\basename($listingFile->getPath()));
            $listingFile->setFileHash(\basename($listingFile->getPath()));
            $listingFile->setFilename(\hash('sha256', '36'));
            $listingFile->setUploadDate($listing->getFirstCreatedDate());
            $listingFile->setImageWidth(1920);
            $listingFile->setImageHeight(1080);
            $listing->addListingFile($listingFile);

            $this->em->persist($listingFile);

            if (!$listing->getMainImage()) {
                $listing->setMainImage($listingFile->getPath());
            }
        }
    }

    private function getUser(): User
    {
        /** @var User $user */
        $user = $this->runtimeCache->get(
            'devGenerateListingsUser',
            function () {
                $qb = $this->em->createQueryBuilder();
                $qb->select('user');
                $qb->from(User::class, 'user');
                $qb->andWhere($qb->expr()->eq('user.email', ':email'));
                $qb->setParameter('email', $this->getUserEmail());
                $qb->orderBy('user.id', Criteria::ASC);
                $user = $qb->getQuery()->getOneOrNullResult();
                if (!$user) {
                    $user = $this->createUserService->getUser($this->getUserEmail());
                    $user->setEnabled(true);
                    $this->em->persist($user);
                    $this->em->flush();
                    $this->logger->info('created user: {userEmail} , password: {userPassword} ', [
                        'userEmail' => $user->getEmail(),
                        'userPassword' => $user->getPlainPassword(),
                    ]);
                    if ($this->outputInterface) {
                        $this->outputInterface->write(\strtr('created user: {userEmail} , password: {userPassword} ', [
                            '{userEmail}' => $user->getEmail(),
                            '{userPassword}' => $user->getPlainPassword(),
                        ]));
                    }
                }

                return $user;
            }
        );

        /** @var User $user */
        $user = $this->em->getReference(User::class, $user->getId());

        return $user;
    }

    private function improvePerformance(): void
    {
        \gc_enable();
        $configuration = $this->em->getConnection()->getConfiguration();
        if (!$configuration instanceof Configuration) {
            throw new \RuntimeException('connection configuration not found');
        }
        $configuration->setSQLLogger();
    }

    private function randomBool(int $probability): bool
    {
        return \random_int(1, 100) <= $probability;
    }

    private function getUserEmail(): string
    {
        return 'user-demo@2max.io';
    }
}
