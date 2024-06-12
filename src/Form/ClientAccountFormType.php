<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

class ClientAccountFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Profil
            ->add('username', TextType::class, [
                'label' => t('User name'),
                // 'purify_html' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 4,
                        'max' => 30]),
                ],
                'attr' => ['placeholder' => t('User name'), 'readonly' => true],
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
                'constraints' => [
                    new Assert\Email(),
                    new NotBlank(),
                    new Length([
                        'min' => 5,
                        'max' => 180]),
                ],
                'attr' => ['placeholder' => t('Email address here'), 'readonly' => true],
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
