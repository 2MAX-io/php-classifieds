<?php

declare(strict_types=1);

namespace App\Form\Listing\Feature;

use App\Form\Listing\SelectPackageType;
use App\Service\Listing\Featured\FeaturedPackageListService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class FeatureType extends AbstractType
{
    /**
     * @var FeaturedPackageListService
     */
    private $featuredPackageListService;

    public function __construct(FeaturedPackageListService $featuredPackageListService)
    {
        $this->featuredPackageListService = $featuredPackageListService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $packages = $this->featuredPackageListService->getPackages($options['listing']);
        $builder->add('package', SelectPackageType::class, [
            'label' => 'trans.Select package',
            'required' => true,
            'choices' => $packages,
            'constraints' => [
                new Constraints\NotBlank(),
                new Constraints\Choice([
                    'choices' => $packages,
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FeatureDto::class,
        ]);

        $resolver->setDefined(['listing']);
    }
}
