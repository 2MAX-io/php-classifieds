<?php

declare(strict_types=1);

namespace App\Form\Listing;

use App\Entity\Category;
use App\Entity\Package;
use App\Repository\CategoryRepository;
use App\Service\Listing\Package\PackagesForListingService;
use Minwork\Helper\Arr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectPackageType extends AbstractType
{
    /**
     * @var PackagesForListingService
     */
    private $packagesForListingService;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        PackagesForListingService $packagesForListingService,
        CategoryRepository $categoryRepository,
        RequestStack $requestStack
    ) {
        $this->packagesForListingService = $packagesForListingService;
        $this->categoryRepository = $categoryRepository;
        $this->requestStack = $requestStack;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'choices' => function (Options $options) {
                return $this->packagesForListingService->getPackages($this->getCategory($options['category']));
            },
            'choice_value' => function (?Package $package) {
                return $package ? $package->getId() : '';
            },
            'choice_name' => function (?Package $package) {
                return $package ? $package->getName() : '';
            },
        ]);
        $resolver->setDefined(['category', 'required']);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getCategory(?Category $currentCategory): ?Category
    {
        $requestData = $this->requestStack->getMasterRequest()->request->all();
        $categoryId = Arr::getNestedElement($requestData, [ListingType::LISTING_FIELD, ListingType::CATEGORY_FIELD]) ?? false;
        $category = $this->categoryRepository->find((int) $categoryId);
        if ($category) {
            return $category;
        }

        /*
         * must be after category from request, to handle cat change when editing
         */
        if ($currentCategory) {
            return $currentCategory;
        }

        return null;
    }
}
