<?php

namespace App\Form\Client\Access;

use App\Entity\User\Manager;
use App\Entity\User\Customer;
use App\Entity\Company\Client;
use App\Entity\User\SalesPerson;
use App\Form\FormListenerFactory;
use Symfony\Component\Form\FormEvents;
use App\Entity\User\SuperAdministrator;
use Symfony\Component\Form\AbstractType;
use App\Repository\Company\ClientRepository;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AccessFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Manager|SalesPerson|SuperAdministrator $employee */
        $employee = $options['employee'];

        $clientOptions = [];

        if ($employee instanceof Manager && $employee instanceof SuperAdministrator && $employee->getMembers()->count() > 1) {
            $clientOptions['group_by'] = fn (Client $client) => $client->getMember()->getName();
        }

        $builder
            ->add('manualDelivery', ChoiceType::class, [
                'expanded' => true,
                'label' => t('Allow the customer to manually enter their shipping address'),
                'required' => true,
                'choices' => [
                    'I authorize my customer to enter their delivery address' => 1,
                    'I receive the lots from my customers to give them to them' => 0,
                ],
            ])
            // Profil
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
                'attr' => ['placeholder' => t('Email address here')],
            ])
            ->add('client', EntityType::class, $clientOptions + [
                'label' => t('Company name of your customer'),
                'class' => Client::class,
                'choice_label' => 'name',
                'query_builder' => fn (ClientRepository $repository) => $repository
                    ->createQueryBuilderClientsByEmployee($employee),
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('employee');
        $resolver->setAllowedTypes('employee', [SalesPerson::class, Manager::class, SuperAdministrator::class]);
        $resolver->setDefault('data_class', Customer::class);
    }
}
