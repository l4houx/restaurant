<?php

namespace App\Form\Data;

use App\Entity\Data\Account;
use App\Entity\User\Manager;
use App\Entity\Data\Transfer;
use App\Entity\User\Customer;
use App\Entity\User\SalesPerson;
use App\Entity\User\Collaborator;
use Symfony\Component\Form\FormView;
use App\Entity\User\SuperAdministrator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use App\Repository\Data\AccountRepository;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class TransferFormType extends AbstractType
{
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var ChoiceGroupView $choiceGroup */
        foreach ($view->children['from']->vars['choices'] as $choiceGroup) {
            usort(
                $choiceGroup->choices,
                fn (ChoiceView $aChoice, ChoiceView $bChoice): int => $aChoice->label <=> $bChoice->label
            );
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Manager|SuperAdministrator $manager */
        $manager = $options['manager'];

        $accountOptions = [
            'query_builder' => fn (AccountRepository $repository) => $repository
                ->createQueryBuilderAccountByManagerForTransfer($manager),
            'choice_label' => function (Account $account) {
                if (null === $account->getUser()) {
                    return sprintf(
                        t('%s - Balance : %d keys'),
                        $account->getMember()->getName(),
                        $account->getBalance()
                    );
                }

                if ($account->getUser() instanceof Customer) {
                    /** @var Customer $customer */
                    $customer = $account->getUser();

                    return sprintf(
                        t('%s - %s - Balance : %d keys'),
                        $customer->getClient()->getName(),
                        $customer->getFullName(),
                        $account->getBalance()
                    );
                }

                /** @var SalesPerson|Manager|Collaborator|SuperAdministrator $employee */
                $employee = $account->getUser();

                return sprintf(
                    t('%s - %s - Balance : %d keys'),
                    $employee->getCrossRoleName(),
                    $employee->getFullName(),
                    $account->getBalance()
                );
            },
            'group_by' => function (Account $account) {
                if (null === $account->getUser()) {
                    return t('Member');
                }

                if ($account->getUser() instanceof Customer) {
                    return t('Client');
                }

                /** @var SalesPerson|Manager|Collaborator|SuperAdministrator $employee */
                $employee = $account->getUser();

                return $employee->getMember()->getName();
            },
        ];

        $builder
            ->add('from', EntityType::class, $accountOptions + [
                'label' => t('From the key account'),
                'class' => Account::class,
            ])
            ->add('to', EntityType::class, $accountOptions + [
                'label' => t('To the key account'),
                'class' => Account::class,
            ])
            ->add('points', IntegerType::class, [
                'label' => t('Amount of the transfer'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Transfer::class);
        $resolver->setRequired('manager');
        $resolver->setAllowedTypes('manager', [Manager::class, SuperAdministrator::class]);
    }
}
