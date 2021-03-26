<?php

declare(strict_types=1);

namespace App\Form\Admin\Settings;

use App\Form\Admin\Settings\Base\SettingTypeInterface;
use App\Helper\ExceptionHelper;
use App\Helper\JsonHelper;
use App\Service\Setting\SettingsDto;
use App\Service\System\License\LicenseValidService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class LicenseSettingsType extends AbstractType implements SettingTypeInterface
{
    /**
     * @var LicenseValidService
     */
    private $licenseValidService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LicenseValidService $licenseValidService,
        LoggerInterface $logger
    ) {
        $this->licenseValidService = $licenseValidService;
        $this->logger = $logger;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('license', TextareaType::class, [
            'label' => 'trans.Enter license for 2MAX.io PHP Classified Ads',
            'required' => true,
            'constraints' => [
                new Callback(['callback' => function (
                    ?string $licenseText,
                    ExecutionContextInterface $context
                ): void {
                    if (empty($licenseText)) {
                        return;
                    }

                    $licenseDecoded = null;
                    try {
                        $licenseDecoded = JsonHelper::toArray(\base64_decode($licenseText));
                    } catch (\Throwable $e) {
                        $this->logger->warning('error while decoding license', ExceptionHelper::flatten($e));
                    }
                    if (!$licenseDecoded) {
                        $this->addViolation('License could not be decoded', $context);

                        return;
                    }
                    if (!$this->licenseValidService::isLicenseValid($licenseText)) {
                        $this->addViolation('License is not correct', $context);
                    }
                }]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => SettingsDto::class,
                'required' => false,
            ]
        );
    }

    /**
     * @param string $message #TranslationKey
     */
    private function addViolation(string $message, ExecutionContextInterface $context): void
    {
        $context->buildViolation($message)
            ->addViolation()
        ;
    }
}
