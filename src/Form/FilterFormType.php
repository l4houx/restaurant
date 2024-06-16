<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use function Symfony\Component\Translation\t;

class FilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('keywords', TextType::class, [
            'label' => false,
            'required' => false,
            'attr' => [
                'class' => 'form-control pe-5',
                'placeholder' => t('Search...'),
            ],
        ]);
    }
}
