<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\User\Customer;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class AccountEditFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Profil
            ->add('username', TextType::class, [
                'label' => t('User name'),
                // 'purify_html' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 4,
                        'max' => 30]),
                ],
                'attr' => ['placeholder' => t('User name'), 'readonly' => true],
            ])
            ->add('firstname', TextType::class, [
                'label' => t('First name'),
                // 'purify_html' => true,
                'required' => true,
                'empty_data' => '',
                'attr' => ['placeholder' => t('First name')],
            ])
            ->add('lastname', TextType::class, [
                'label' => t('Last name'),
                // 'purify_html' => true,
                'required' => true,
                'empty_data' => '',
                'attr' => ['placeholder' => t('Last name')],
            ])
            // Contact
            ->add('email', EmailType::class, [
                'label' => t('Email address'),
                // 'purify_html' => true,
                'required' => true,
                'empty_data' => '',
                'constraints' => [
                    new Assert\Email(),
                    new NotBlank(),
                    new Length([
                        'min' => 5,
                        'max' => 180]),
                ],
                'attr' => ['placeholder' => t('Email address here'), 'readonly' => true],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();

                /** @var User $user */
                $user = $event->getData();

                if ($user instanceof Customer) {
                    return;
                }

                $form->add('phone', TextType::class, [
                    "label" => "Telephone No",
                    // 'purify_html' => true,
                    'required' => true,
                    'empty_data' => '',
                    'attr' => ['placeholder' => t('Telephone No here')],
                ]);
            })
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
