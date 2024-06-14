<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use function Symfony\Component\Translation\t;

class RulesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("refuse", SubmitType::class, [
                "label" => t("Refuse"),
                "attr" => [
                    "class" => 'btn btn-danger'
                ]
            ])
            ->add("accept", SubmitType::class, [
                "label" => t("Accept"),
                "attr" => [
                    "class" => 'btn btn-success'
                ]
            ])
        ;
    }
}
