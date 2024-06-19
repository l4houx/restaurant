<?php

namespace App\Form\Client\Company;

use App\Form\AddressType;
use App\Entity\User\Manager;
use App\Entity\Company\Client;
use App\Entity\Company\Member;
use App\Entity\User\SalesPerson;
use App\Form\FormListenerFactory;
use Symfony\Component\Form\FormEvents;
use App\Entity\User\SuperAdministrator;
use Symfony\Component\Form\AbstractType;
use App\Repository\Company\MemberRepository;
use function Symfony\Component\Translation\t;
use App\Repository\User\SalesPersonRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CompanyFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => t('Social reason'),
                'empty_data' => '',
                'attr' => ['placeholder' => t('Company')],
            ])
            ->add('address', AddressType::class, [
                'label' => false,
            ])
            ->add('companyNumber', TextType::class, [
                'required' => false,
                'label' => t('SIRET No'),
                'empty_data' => '',
                'attr' => ['placeholder' => t('21931232314430')],
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;

        $builder->get('address')->remove('email');
        $builder->get('address')->remove('phone');

        /** @var Manager|SuperAdministrator $employee */
        $employee = $options['employee'];

        $memberOptions = [];

        if ($employee->getMembers()->count() > 1) {
            $memberOptions = ['group_by' => fn (SalesPerson $salesPerson) => $salesPerson->getMember()->getName()];
        }

        $builder->add('salesPerson', EntityType::class, $memberOptions + [
            'required' => false,
            'label' => t('Sales Person'),
            'placeholder' => t('Not specified'),
            'class' => SalesPerson::class,
            'autocomplete' => true,
            'multiple' => false,
            'choice_label' => fn (SalesPerson $salesPerson) => $salesPerson->getFullName(),
            'query_builder' => fn (SalesPersonRepository $salesPersonRepository) => $salesPersonRepository
                ->createQueryBuilderSalesPersonsByManager($employee),
        ]);

        if ($employee->getMembers()->count() > 1) {
            $builder->add('member', EntityType::class, [
                'label' => t('Member'),
                'class' => Member::class,
                'choice_label' => 'name',
                'query_builder' => fn (MemberRepository $repository) => $repository
                    ->createQueryBuilderMembersByManager($employee),
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('employee');
        $resolver->setAllowedTypes('employee', [Manager::class, SuperAdministrator::class]);
        $resolver->setDefault('data_class', Client::class);
    }
}
