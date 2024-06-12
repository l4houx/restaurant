<?php

namespace App\Form;

use App\Entity\Post;
use App\Form\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

use function Symfony\Component\Translation\t;

class PostFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => 'scale',
                'label' => t('Main blog post image'),
                'translation_domain' => 'messages',
            ])
            ->add('name', TextType::class, [
                'label' => t('Name'),
                'required' => true,
                // 'purify_html' => true,
                'empty_data' => '',
                'help' => t('Keep your post names under 10 characters. Write heading that describe the topic content. Contextualize for Your Post.'),
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
            ->add('type', PostTypeAutocompleteField::class)
            // ->add('author', UserAutocompleteField::class, ['label' => t('Author :')])
            ->add('category', PostCategoryAutocompleteField::class)
            ->add('tags', TextType::class, [
                'label' => t('Tags'),
                // 'purify_html' => true,
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'class' => 'tags-input',
                    'data-limit' => 1,
                ],
                'help' => t('Make sure you select the correct keyword to allow users to find it quickly.'),
            ])
            ->add('readtime', TextType::class, [
                'label' => t('Reading time in minutes'),
                'required' => false,
                // 'purify_html' => true,
                'attr' => ['class' => 'touchspin-integer', 'data-min' => 1, 'data-max' => 1000000],
            ])
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
            ->add('publishedAt', null, [
                'label' => t('Published the'),
                'required' => false,
                'empty_data' => '',
                'widget' => 'single_text',
            ])
            ->add('isOnline', SwitchType::class, ['label' => t('Online')])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->slug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
