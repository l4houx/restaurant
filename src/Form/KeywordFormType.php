<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use function Symfony\Component\Translation\t;

class KeywordFormType extends AbstractType
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
