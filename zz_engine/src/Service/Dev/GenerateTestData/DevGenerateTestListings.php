<?php

declare(strict_types=1);

namespace App\Service\Dev\GenerateTestData;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\CustomFieldOption;
use App\Entity\Listing;
use App\Entity\ListingCustomFieldValue;
use App\Entity\User;
use App\Helper\Arr;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class DevGenerateTestListings
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(EntityManagerInterface $em, CacheInterface $cache)
    {
        $this->em = $em;
        $this->cache = $cache;
    }

    public function generate(int $count): void
    {
        \gc_enable();
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $faker = Factory::create();

        $persistCount = 0;
        for ($i=0; $i<$count; $i++) {
            $listing = new Listing();
            $listing->setTitle($faker->text(30));
            $listing->setDescription($faker->sentence(30));
            $listing->setUser($this->em->getReference(User::class, 1));
            $listing->setCategory($this->getRandomCategory());
            $listing->setEmailShow(true);
            $listing->setFirstCreatedDate(new \DateTime());
            $listing->setLastEditDate(new \DateTime());
            $listing->setOrderByDate(new \DateTime());
            $listing->setEmail('user@example.com');
            $listing->setPhone('12345555555');
            $listing->setSlug('test');
            $listing->setSearchText($listing->getTitle() . ' ' . $listing->getDescription());
            $listing->setValidUntilDate(Carbon::now()->addDays(7));

            if (\random_int(1,100) > 20) {
                $listing->setPrice(\random_int(1, 40000000) / 100);
            }

            $this->setCustomFields($listing);

            $this->em->persist($listing);

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
        foreach ($listing->getCategory()->getCustomFields() as $customField) {
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

        if (!$customField->getRequired()) {
            if (\random_int(1,100) < 10) {
                return;
            }
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

        if (!$customField->getRequired()) {
            if (\random_int(1,100) < 10) {
                return;
            }
        }

        $customFieldValue = new ListingCustomFieldValue();
        $customFieldValue->setCustomField($customField);
        $customFieldValue->setListing($listing);
        $customFieldValue->setValue((string) \random_int(1, 300000));
        $this->em->persist($customFieldValue);
    }

    private function getRandomCategory(): Category
    {
        $categoryList = $this->cache->get('devGenerateListingsCategories', function() {
            return $this->em->getRepository(Category::class)->findBy(['lvl' => 2]);
        });

        /** @var Category $category */
        $category = Arr::random($categoryList);
        $category = $this->em->merge($category);

        return $category;
    }
}
