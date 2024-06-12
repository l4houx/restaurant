<?php

namespace App\Form;

use App\Form\Type\SwitchType;
use App\Service\SettingService;
use App\Entity\HelpCenterArticle;
use App\Entity\HelpCenterCategory;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class HelpCenterArticleFormType extends AbstractType
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
                //'purify_html' => true,
                'empty_data' => '',
                'help' => t('Keep your article names under 10 characters. Write heading that describe the topic content. Contextualize for Your Article.'),
            ])
            ->add('slug', TextType::class, [
                'label' => t('Slug'),
                'empty_data' => '',
                'required' => false,
                'help' => t('Field must contain an unique value.'),
            ])
            ->add('content', TextareaType::class, [
                'label' => t('Content'),
                'required' => true,
                // 'purify_html' => true,
                'empty_data' => '',
                'attr' => ['class' => 'wysiwyg', 'placeholder' => '', 'rows' => 6],
                'help' => t(''),
            ])
            ->add('category', HelpCenterCategoryAutocompleteField::class)
            ->add('tags', TextType::class, [
                'label' => t('Tags'),
                //'purify_html' => true,
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'class' => 'tags-input',
                    'data-limit' => 1,
                ],
                'help' => t('Make sure you select the correct keyword to allow users to find it quickly.'),
            ])
            /*
            ->add('category', EntityType::class, [
                'label' => t('Category'),
                'required' => true,
                'multiple' => false,
                'class' => HelpCenterCategory::class,
                'choice_label' => 'name',
                'autocomplete' => true,
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-select',
                    'data-limit' => 1,
                ],
                'help' => t('Make sure to select right category to let the users find it quickly'),
                'query_builder' => function () {
                    return $this->settingService->getHelpCenterCategories([]);
                },
            ])
            */
            ->add('metaTitle', TextType::class, [
                'label' => t('Meta title'),
                'required' => false,
                // 'purify_html' => true,
                'empty_data' => '',
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => t('Meta description'),
                'required' => false,
                'empty_data' => '',
                'attr' => ['placeholder' => '', 'rows' => 6],
                'help' => t(''),
            ])
            ->add('isOnline', SwitchType::class, ['label' => t('Online')])
            ->add('isFeatured', SwitchType::class, ['label' => t('Featured')])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->slug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HelpCenterArticle::class,
        ]);
    }
}
