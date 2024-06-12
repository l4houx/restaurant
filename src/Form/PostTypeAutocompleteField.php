<?php

namespace App\Form;

use App\Entity\PostType;
use App\Repository\PostTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

use function Symfony\Component\Translation\t;

#[AsEntityAutocompleteField]
class PostTypeAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => t('Type'),
            'class' => PostType::class,
            'placeholder' => t('Choose a type'),
            'choice_label' => 'name',
            'required' => true,
            'attr' => ['data-limit' => 1],
            'query_builder' => function (PostTypeRepository $postTypeRepository) {
                return $postTypeRepository->createQueryBuilder('t');
            },
            'help' => t('Make sure you select the correct post type to allow users to find it quickly.'),
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
