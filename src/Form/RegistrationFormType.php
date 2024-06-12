<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class RegistrationFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        # code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $passwordAttrs = ['minlength' => 16];
        $builder
            // Profil
            ->add('username', TextType::class, [
                'label' => t('User name'),
                // 'purify_html' => true,
                'required' => true,
                'empty_data' => '',
                'attr' => ['placeholder' => t('User name')],
            ])
            ->add('firstname', TextType::class, [
                'label' => t('First name'),
                // 'purify_html' => true,
                'required' => true,
                'empty_data' => '',
                'attr' => ['placeholder' => t('First name')],
            ])
            ->add('lastname', TextType::class, [
                'label' => t('Last name'),
                // 'purify_html' => true,
                'required' => true,
                'empty_data' => '',
                'attr' => ['placeholder' => t('Last name')],
            ])
            // Contact
            ->add('email', EmailType::class, [
                'label' => t('Email address'),
                // 'purify_html' => true,
                'required' => true,
                'attr' => ['placeholder' => t('Email address here')],
            ])
            ->add('isAgreeTerms', CheckboxType::class, [
                'mapped' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => t('You must accept the conditions of use of your personal data.'),
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    // 'purify_html' => true,
                    'toggle' => true,
                    'translation_domain' => 'messages',
                    'attr' => [
                        'placeholder' => t('Password'),
                        'autocomplete' => 'new-password',
                    ],
                ],
                'label_attr' => ['class' => 'form-label'],
                'first_options' => ['label' => t('Password'), 'attr' => [...$passwordAttrs, ...['placeholder' => '**************']]],
                'second_options' => ['label' => t('Confirm password'), 'attr' => [...$passwordAttrs, ...['placeholder' => '**************']]],
                'invalid_message' => t('Password fields must correspond.'),
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => t('Please enter a password'),
                    ]),
                    new Length([
                        'min' => 16,
                        'minMessage' => t('Your password must have at least {{ limit }} characters'),
                        'max' => 4096,
                    ]),
                    new PasswordStrength(
                        minScore: PasswordStrength::STRENGTH_STRONG
                    ),
                ],
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
