<?php

declare(strict_types=1);

namespace App\Form\User\Setting;

use App\Entity\User;
use App\Form\Type\BoolRequiredType;
use App\Security\CurrentUserService;
use App\Service\Setting\SettingsDto;
use App\Twig\TwigUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserSettingsType extends AbstractType
{
    /**
     * @var TwigUser
     */
    private $twigUser;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var SettingsDto
     */
    private $settingsDto;

    public function __construct(TwigUser $twigUser, CurrentUserService $currentUserService, SettingsDto $settingsDto)
    {
        $this->twigUser = $twigUser;
        $this->currentUserService = $currentUserService;
        $this->settingsDto = $settingsDto;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('displayUsername', TextType::class, [
            'label' => 'trans.Person to contact or company name',
            'required' => false,
            'attr' => [
                'id' => 'display-username',
                'placeholder' => $this->twigUser->displayUserName($this->currentUserService->getUser()),
                'autocomplete' => 'name',
            ],
            'constraints' => [
                new Length(['max' => 36, 'allowEmptyString' => false]),
            ],
        ]);

        if ($this->settingsDto->getMessageSystemEnabled()) {
            $builder->add('notificationByEmailNewMessage', BoolRequiredType::class, [
                'label' => 'trans.Notify by email about new message',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
            $builder->add('messagesEnabled', BoolRequiredType::class, [
                'label' => 'trans.Direct messages enabled',
                'help' => 'trans.Write message button is displayed for your listings',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
