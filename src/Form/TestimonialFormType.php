<?php

namespace App\Form;

use App\Entity\Testimonial;
use App\Form\Type\SwitchType;
use App\Entity\Traits\HasRoles;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TestimonialFormType extends AbstractType
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authChecker,
        private FormListenerFactory $formListenerFactory
    ) {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                //'label' => t('Name'),
                'label' => false,
                'required' => true,
                'attr' => ['placeholder' => t('Name *')],
                // 'purify_html' => true,
                'empty_data' => '',
            ])
            ->add('slug', TextType::class, [
                //'label' => t('Slug'),
                'label' => false,
                'empty_data' => '',
                'required' => false,
                'attr' => ['placeholder' => t('Slug')],
                //'help' => t('Field must contain an unique value.'),
            ])
            ->add('rating', ChoiceType::class, [
                //'label' => t('Your rating (out of 5 stars)'),
                'label' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices' => ['★★★★★ (5/5)' => 5, '★★★★☆ (4/5)' => 4, '★★★☆☆ (3/5)' => 3, '★★☆☆☆ (2/5)' => 2, '★☆☆☆☆ (1/5)' => 1],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
            ])
            ->add('content', TextareaType::class, [
                //'label' => t('Content'),
                'label' => false,
                'required' => true,
                'empty_data' => '',
                'attr' => ['placeholder' => t('Your content *'), 'rows' => 3],
                'help' => t(''),
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->slug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;

        if ($this->authChecker->isGranted(HasRoles::ADMINAPPLICATION)) {
            $builder
                ->add('author', UserAutocompleteField::class, [
                    //'label' => t('Author')
                    'label' => false,
                ])
                ->add('isOnline', SwitchType::class, ['label' => t('Online')])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Testimonial::class,
        ]);
    }
}
