<?php

namespace App\Form;

use App\DataTransferObject\ContactFormDTO;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class ContactFormType extends AbstractType
{
    public function __construct(
        private readonly ParameterBagInterface $parameter
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => t('Name'),
                'empty_data' => '',
                'required' => true,
                'attr' => [
                    'class'  => 'form-control bg-transparent',
                    'placeholder' => t('Your name')
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => t('Email address'),
                'empty_data' => '',
                'required' => true,
                'attr' => [
                    'class'  => 'form-control bg-transparent',
                    'placeholder' => t('Email address')
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => t('Message'),
                'empty_data' => '',
                'required' => true,
                'attr' => [
                    'style' => 'height: 100px',
                    'class'  => 'form-control bg-transparent',
                    'placeholder' => t('Leave a comment here')
                ],
            ])
            ->add('service', ChoiceType::class, options: [
                'label' => t('Choose a service'),
                'required' => true,
                'choices' => [
                    'Support' => $this->parameter->get('website_support'),
                    'Marketing' => $this->parameter->get('website_marketing'),
                    'Accounting' => $this->parameter->get('website_compta'),
                ],
                'attr' => [
                    'class'  => 'form-control bg-transparent',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactFormDTO::class,
        ]);
    }
}
