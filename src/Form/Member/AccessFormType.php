<?php

namespace App\Form\Member;

use App\Entity\User;
use App\Entity\User\Manager;
use App\Entity\Company\Member;
use App\Form\FormListenerFactory;
use Symfony\Component\Form\FormEvents;
use App\Entity\User\SuperAdministrator;
use Symfony\Component\Form\AbstractType;
use App\Repository\Company\MemberRepository;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AccessFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ->add('phone', TextType::class, [
                'label' => 'Telephone No',
                // 'purify_html' => true,
                'required' => true,
                'empty_data' => '',
                'attr' => ['placeholder' => t('Telephone No here')],
            ])
            // Team
            ->add('designation', TextType::class, [
                'label' => t('Designation'),
                // 'purify_html' => true,
                'required' => false,
                'empty_data' => '',
                'attr' => ['placeholder' => ''],
            ])
            ->add('about', TextareaType::class, [
                'label' => t('About'),
                // 'purify_html' => true,
                'required' => false,
                'empty_data' => '',
                'attr' => ['placeholder' => '', 'rows' => 6],
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;

        /** @var Manager|SuperAdministrator $manager */
        $manager = $options['manager'];

        if ($manager->getMembers()->count() > 1) {
            $builder->add('member', EntityType::class, [
                'label' => t('Member'),
                'class' => Member::class,
                'choice_label' => 'name',
                'query_builder' => fn (MemberRepository $repository) => $repository->createQueryBuilder('m')
                    ->where('m.id IN (:members)')
                    ->setParameter(
                        'members',
                        $manager->getMembers()->map(fn (Member $member) => $member->getId())->toArray()
                    ),
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('manager');
        $resolver->setAllowedTypes('manager', [Manager::class, SuperAdministrator::class]);
        $resolver->setDefault('data_class', User::class);
    }
}
