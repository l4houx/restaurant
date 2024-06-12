<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $editAttr = ['minlength' => 16];

        if ($options['current_password_is_required']) {
            $builder
                ->add('currentPassword', PasswordType::class, [
                    'label' => t('Current Password'),
                    'mapped' => false,
                    'toggle' => true,
                    'attr' => [
                        'autocomplete' => 'off',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => t('Please enter your current password'),
                        ]),
                        new UserPassword(['message' => t('Current password is invalid.')]),
                    ],
                ])
            ;
        }

        $builder
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        // 'purify_html' => true,
                        'autocomplete' => 'new-password',
                        'toggle' => true,
                        'translation_domain' => 'messages',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => t('Please enter a password'),
                        ]),
                        new Length([
                            'min' => 16,
                            'minMessage' => t('Your password must have at least {{ limit }} characters'),
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new PasswordStrength(),
                        new NotCompromisedPassword(),
                    ],
                    'label' => t('New password'),
                    'attr' => [...$editAttr, ...['placeholder' => '**************']],
                ],
                'second_options' => [
                    'label' => t('Confirm Your New Password'),
                    'attr' => [...$editAttr, ...['placeholder' => '**************']],
                ],
                'translation_domain' => 'messages',
                'invalid_message' => t('The password fields must match.'),
                'mapped' => false,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'current_password_is_required' => false,
        ]);
        $resolver->setAllowedTypes('current_password_is_required', 'bool');
    }
}
