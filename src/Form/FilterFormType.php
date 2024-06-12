<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Filter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class FilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('keywords', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => t('Search for a product...'),
                ],
            ])
            ->add('min', HiddenType::class)
            ->add('max', HiddenType::class)
            ->add('brand', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Brand::class,
                'choice_label' => 'name',
                'placeholder' => t('All brands'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Filter::class);
        $resolver->setDefault('method', Request::METHOD_GET);
        $resolver->setDefault('attr', [
            'id' => 'filter',
            'class' => 'collapse nav flex-column',
            'data-bs-parent' => '#subCategories',
        ]);
    }
}
