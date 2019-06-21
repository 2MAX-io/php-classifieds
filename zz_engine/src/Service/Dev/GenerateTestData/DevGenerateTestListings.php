<?php

declare(strict_types=1);

namespace App\Service\Dev\GenerateTestData;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\CustomFieldOption;
use App\Entity\Listing;
use App\Entity\ListingCustomFieldValue;
use App\Entity\ListingFile;
use App\Entity\User;
use App\Helper\Arr;
use App\Helper\SlugHelper;
use App\Service\System\Cache\RuntimeCacheService;
use App\Service\System\Sort\SortService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;

class DevGenerateTestListings
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RuntimeCacheService
     */
    private $runtimeCache;

    public function __construct(EntityManagerInterface $em, RuntimeCacheService $runtimeCache)
    {
        $this->em = $em;
        $this->runtimeCache = $runtimeCache;
    }

    public function generate(int $count): void
    {
        \gc_enable();
        $this->em->getConnection()->getConfiguration()->setSQLLogger();

        $faker = Factory::create();
        $currentDate = Carbon::now()->seconds(0);

        $persistCount = 0;
        for ($i=0; $i<$count; $i++) {
            $listing = new Listing();
            $listing->setTitle($faker->text(30));
            $listing->setDescription($faker->sentence(30));
            $listing->setUser($this->getUser());
            $listing->setCategory($this->getRandomCategory());
            $listing->setEmailShow(true);
            $listing->setFirstCreatedDate($currentDate);
            $listing->setLastEditDate($currentDate);
            $listing->setOrderByDate($currentDate);
            $listing->setEmail('user@example.com');
            $listing->setPhone('12345555555');
            $listing->setSlug(SlugHelper::getSlug($listing->getTitle()));
            $listing->setSearchText($listing->getTitle() . ' ' . $listing->getDescription());
            $listing->setValidUntilDate(Carbon::now()->addDays(7));

            if (\random_int(0, 100) > 80) {
                $listing->setFeatured(true);
                $listing->setFeaturedUntilDate($listing->getValidUntilDate());
            }

            if (\random_int(1,100) > 20) {
                $listing->setPrice(\random_int(1, 40000000) / 100);
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

    public function addMultipleCustomFieldValues(Listing $listing, CustomField $customField): void
    {
        if (!$customField->getCustomFieldOptions()->count()) {
            return;
        }

        $used = [];
        $requestedNumber = 1;
        if ($customField->getType() === CustomField::TYPE_CHECKBOX_MULTIPLE) {
            $requestedNumber = \random_int(1, $customField->getCustomFieldOptions()->count());
        }

        $foundCount = 0;
        $retryCount = 0;
        while ($foundCount < $requestedNumber && $retryCount < 50) {
            $listingCustomFieldValue = $this->addCustomFieldValue($listing, $customField);
            if (Arr::inArray($listingCustomFieldValue->getValue(), $used)) {
                $listing->removeListingCustomFieldValue($listingCustomFieldValue);
                $this->em->remove($listingCustomFieldValue);
                $retryCount++;
                continue;
            }
            $used[] = $listingCustomFieldValue->getValue();
            $foundCount++;
        }
    }

    public function addCustomFieldValue(Listing $listing, CustomField $customField): ListingCustomFieldValue
    {
        /** @var CustomFieldOption $randomOption */
        $randomOption = Arr::random($customField->getCustomFieldOptions()->toArray());

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
        if ($customField->getType() !== CustomField::TYPE_YEAR_RANGE) {
            return;
        }

        if (!$customField->getRequired() && \random_int(1,100) < 10) {
            return;
        }

        $customFieldValue = new ListingCustomFieldValue();
        $customFieldValue->setCustomField($customField);
        $customFieldValue->setListing($listing);
        $customFieldValue->setValue((string) \random_int(1980, (int) \date('Y')));
        $this->em->persist($customFieldValue);
    }

    private function setIntegerRange(Listing $listing, CustomField $customField): void
    {
        if ($customField->getType() !== CustomField::TYPE_INTEGER_RANGE) {
            return;
        }

        if (!$customField->getRequired() && \random_int(1,100) < 10) {
            return;
        }

        $customFieldValue = new ListingCustomFieldValue();
        $customFieldValue->setCustomField($customField);
        $customFieldValue->setListing($listing);
        $customFieldValue->setValue((string) \random_int(1, 300000));
        $this->em->persist($customFieldValue);
    }

    private function getRandomCategory(): Category
    {
        $categoryList = $this->runtimeCache->get('devGenerateListingsCategories', function() {
            return $this->em->getRepository(Category::class)->findBy(['lvl' => 2]);
        });

        /** @var Category $category */
        $category = Arr::random($categoryList);
        $category = $this->em->merge($category);

        return $category;
    }

    private function addFiles(Listing $listing): void
    {
        $count = 0;
        if (\random_int(0, 100) > 10) {
            $count = \random_int(1,3);
        }

        $sort = SortService::FIRST_VALUE;
        for ($i=0; $i<$count; ++$i) {
            $sort++;
            $listingFile = new ListingFile();
            $listingFile->setPath(
                Arr::random(
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
            $listingFile->setSizeBytes(1);
            $listingFile->setMimeType('image/jpeg');
            $listingFile->setFilename(\basename($listingFile->getPath()));
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
                $qb = $this->em->getRepository(User::class)->createQueryBuilder('user');
                $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->eq('user.email', ':email'),
                    $qb->expr()->eq('user.id', 1),
                ));
                $qb->setParameter('email', 'user-demo@2max.io');
                $qb->orderBy('user.id', 'DESC');
                $user = $qb->getQuery()->getOneOrNullResult();

                if (!$user) {
                    throw new \UnexpectedValueException('user: user-demo@2max.io not found');
                }

                return $user;
            }
        );

        $user = $this->em->merge($user);

        return $user;
    }
}
