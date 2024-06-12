<?php

namespace App\Form;

use App\Entity\HelpCenterCategory;
use Symfony\Component\Form\AbstractType;
use function Symfony\Component\Translation\t;
use App\Repository\HelpCenterCategoryRepository;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class HelpCenterCategoryAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => t('Categorie'),
            'class' => HelpCenterCategory::class,
            'placeholder' => t('Choose a category'),
            'choice_label' => 'name',
            'required' => true,
            'multiple' => false,
            'attr' => ['data-limit' => 1],
            'query_builder' => function (HelpCenterCategoryRepository $helpCenterCategoryRepository) {
                return $helpCenterCategoryRepository->createQueryBuilder('c');
            },
            'help' => t('Make sure you select the correct category to allow users to find it quickly.'),
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
