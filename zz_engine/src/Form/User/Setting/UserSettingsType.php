<?php

declare(strict_types=1);

namespace App\Form\User\Setting;

use App\Security\CurrentUserService;
use App\Twig\TwigUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class UserSettingsType extends AbstractType
{
    public const DISPLAY_USERNAME = 'displayUsername';

    /**
     * @var TwigUser
     */
    private $twigUser;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    public function __construct(TwigUser $twigUser, CurrentUserService $currentUserService)
    {
        $this->twigUser = $twigUser;
        $this->currentUserService = $currentUserService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(static::DISPLAY_USERNAME, TextType::class, [
            'label' => 'trans.Person to contact or company name',
            'required' => false,
            'attr' => [
                'id' => 'display-username',
                'placeholder' => $this->twigUser->displayUserName($this->currentUserService->getUser()),
            ],
            'constraints' => [
                new Length(['max' => 36]),
            ],
        ]);
    }
}
