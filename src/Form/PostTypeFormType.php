<?php

namespace App\Form;

use App\Entity\PostType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class PostTypeFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => t('Name'),
                'required' => true,
                // 'purify_html' => true,
                'empty_data' => '',
            ])
            ->add('slug', TextType::class, [
                'label' => t('Slug'),
                'empty_data' => '',
                'required' => false,
                'help' => t('Field must contain an unique value.'),
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->slug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostType::class,
        ]);
    }
}
