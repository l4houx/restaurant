<?php

namespace App\Form;

use App\Entity\Shipping;
use App\Repository\ShippingRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

use function Symfony\Component\Translation\t;

#[AsEntityAutocompleteField]
class CountryAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => t('Country'),
            'class' => Shipping::class,
            'placeholder' => t('Choose a country'),
            'choice_label' => 'name',
            'required' => true,
            'constraints' => [
                new NotBlank(['groups' => ['user']]),
            ],
            'query_builder' => function (ShippingRepository $shippingRepository) {
                return $shippingRepository->createQueryBuilder('s');
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
