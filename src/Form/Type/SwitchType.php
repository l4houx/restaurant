<?php

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SwitchType extends CheckboxType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
            'attr' => [
                'is' => 'input-switch',
                'class' => 'me-2',
                'style' => 'margin-left: -24px;',
            ],
            'row_attr' => [
                'class' => 'form-check form-switch mb-4'
            ],
        ]);
    }
}
