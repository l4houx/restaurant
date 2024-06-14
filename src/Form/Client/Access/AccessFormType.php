<?php

namespace App\Form\Client\Access;

use App\Entity\Company\Client;
use App\Entity\User\Customer;
use App\Entity\User\Manager;
use App\Entity\User\SalesPerson;
use App\Form\FormListenerFactory;
use App\Repository\Company\ClientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class AccessFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Manager|SalesPerson $employee */
        $employee = $options['employee'];

        $clientOptions = [];

        if ($employee instanceof Manager && $employee->getMembers()->count() > 1) {
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
            ->add('firstname', TextType::class, [
                'label' => t('First name'),
                'empty_data' => '',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Last name',
                'empty_data' => '',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email address',
                'empty_data' => '',
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
        $resolver->setAllowedTypes('employee', [SalesPerson::class, Manager::class]);
        $resolver->setDefault('data_class', Customer::class);
    }
}
