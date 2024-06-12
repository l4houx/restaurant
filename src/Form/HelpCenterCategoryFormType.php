<?php

namespace App\Form;

use App\Entity\HelpCenterCategory;
use App\Form\Type\IconType;
use App\Form\Type\SwitchType;
use App\Service\SettingService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

// use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class HelpCenterCategoryFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*
                ->add('parent', EntityType::class, [
                    'required' => false,
                    'multiple' => false,
                    'class' => HelpCenterCategory::class,
                    'choice_label' => 'name',
                    'attr' => ['class' => 'select2'],
                    'label' => 'Parent',
                    'help' => t('Select the parent category to add a sub category'),
                    'query_builder' => function() {
                        return $this->settingService->getHelpCenterCategories(["parent" => "none"]);
                    }
                ])
            */
            ->add('name', TextType::class, [
                'label' => t('Name'),
                'required' => true,
                // 'purify_html' => true,
                'empty_data' => '',
                'help' => t('Keep your category names under 10 characters. Write heading that describe the topic content. Contextualize for Your Category.'),
            ])
            ->add('slug', TextType::class, [
                'label' => t('Slug :'),
                'empty_data' => '',
                'required' => false,
                'help' => t('Field must contain an unique value.'),
            ])
            ->add('icon', IconType::class)
            ->add('color', ColorType::class, [
                'label' => t('Color'),
                // 'purify_html' => true,
                'empty_data' => '',
                'required' => false,
            ])
            ->add('isOnline', SwitchType::class, ['label' => t('Online')])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->slug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HelpCenterCategory::class,
        ]);
    }
}
