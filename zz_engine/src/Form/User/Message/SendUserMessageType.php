<?php

declare(strict_types=1);

namespace App\Form\User\Message;

use App\Form\User\Message\Dto\SendUserMessageDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SendUserMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('message', TextareaType::class, [
            'label' => false,
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 1, 'max' => 3600, 'allowEmptyString' => false]),
            ],
            'attr' => [
                'class' => 'form-control border-0 py-2 bg-primary rounded text-white user-message-textarea js__messageTextarea',
                'placeholder' => 'trans.Enter your message here',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => SendUserMessageDto::class,
            ]
        );
    }
}
