<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('streetAddress', TextType::class, [
                // 'purify_html' => true,
                'required' => true,
                'label' => t('Address'),
                'empty_data' => '',
                'attr' => ['placeholder' => t('1 rue de la mairie')],
            ])
            ->add('restAddress', TextType::class, [
                // 'purify_html' => true,
                'required' => false,
                'label' => t('Additional address'),
            ])
            ->add('zipCode', TextType::class, [
                // 'purify_html' => true,
                'required' => true,
                'label' => t('Postal code'),
                'empty_data' => '',
                'attr' => ['placeholder' => t('75000')],
            ])
            ->add('locality', TextType::class, [
                // 'purify_html' => true,
                'required' => true,
                'label' => t('City'),
                'empty_data' => '',
                'attr' => ['placeholder' => t('Paris')],
            ])
            ->add('phone', TextType::class, [
                // 'purify_html' => true,
                'required' => false,
                'label' => t('Telephone No'),
                'empty_data' => '',
                'attr' => ['placeholder' => t('Telephone No here')],
            ])
            ->add('email', EmailType::class, [
                // 'purify_html' => true,
                'required' => false,
                'label' => t('E-mail address'),
                'empty_data' => '',
                'attr' => ['placeholder' => t('Email address here')],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Address::class);
    }
}
