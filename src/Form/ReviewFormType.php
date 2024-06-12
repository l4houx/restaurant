<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class ReviewFormType extends AbstractType
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
            /*
            ->add('slug', TextType::class, [
                'label' => t('Slug'),
                'empty_data' => '',
                'required' => false,
                'help' => t('Field must contain an unique value.'),
            ])
            */
            ->add('rating', ChoiceType::class, [
                'label' => t('Your rating (out of 5 stars)'),
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices' => ['★★★★★ (5/5)' => 5, '★★★★☆ (4/5)' => 4, '★★★☆☆ (3/5)' => 3, '★★☆☆☆ (2/5)' => 2, '★☆☆☆☆ (1/5)' => 1],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
            ])
            ->add('content', TextareaType::class, [
                'label' => t('Content'),
                'required' => true,
                'empty_data' => '',
                'attr' => ['placeholder' => '', 'rows' => 6],
                'help' => t(''),
            ])
            // ->add('author', UserAutocompleteField::class, ['label' => t('Author')])
            // ->add('isVisible', SwitchType::class, ['label' => t('Online')])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->slug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
