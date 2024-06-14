<?php

namespace App\Form\Member;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use function Symfony\Component\Translation\t;

class FilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('keywords', TextType::class, [
            'label' => t('Search'),
            'required' => false,
            'attr' => [
                'placeholder' => t('Search'),
            ],
        ]);
    }
}
