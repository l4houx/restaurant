<?php

namespace App\Form\Data;

use App\Entity\Data\Account;
use App\Entity\User\Manager;
use App\Entity\Data\Purchase;
use App\Entity\User\SuperAdministrator;
use Symfony\Component\Form\AbstractType;
use App\Repository\Data\AccountRepository;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class PurchaseFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('points', IntegerType::class, [
                'label' => t('Amount of your purchase'),
                'empty_data' => 0,
                'help' => t('Reminder: 1 star = 1 euro excl. tax'),
            ])
            ->add('internReference', TextType::class, [
                'label' => t('Your internal reference (visible on the invoice)'),
                'required' => false,
            ])
            ->add('mode', ChoiceType::class, [
                'label' => t('Payment method'),
                'expanded' => true,
                'choices' => [
                    Purchase::MODE_BANK_WIRE => Purchase::MODE_BANK_WIRE,
                    Purchase::MODE_CHECK => Purchase::MODE_CHECK,
                ],
            ]);

        /** @var Manager|SuperAdministrator $manager */
        $manager = $options['manager'];

        if ($manager->getMembers()->count() > 1) {
            $builder->add('account', EntityType::class, [
                'label' => 'Key Account',
                'class' => Account::class,
                'choice_name' => fn (Account $account) => $account->getMember()->getName(),
                'query_builder' => fn (AccountRepository $repository) => $repository
                    ->createQueryBuilderAccountByManagerForPurchase($manager),
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Purchase::class);
        $resolver->setDefault('validation_groups', ['new']);
        $resolver->setRequired('manager');
        $resolver->setAllowedTypes('manager', [Manager::class, SuperAdministrator::class]);
    }
}
