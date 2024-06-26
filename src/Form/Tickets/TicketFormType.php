<?php

namespace App\Form\Tickets;

use App\Entity\User;
use App\Entity\Tickets\Level;
use App\Entity\Tickets\Status;
use App\Entity\Tickets\Ticket;
use App\Form\FormListenerFactory;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use App\Repository\Tickets\LevelRepository;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TicketFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                'label' => t('Subject'),
                'required' => true,
                'attr' => ['placeholder' => t('Your subject')],
            ])
            ->add('content', TextareaType::class, [
                'label' => t("Content"),
                //'purify_html' => true,
                'required' => true,
                'empty_data' => '',
                'attr' => ['placeholder' => t('Your content'), 'rows' => 4],
                'help' => t(''),
            ])
            ->add('level', EntityType::class, [
                'label' => t('Level'),
                'class' => Level::class,
                'choice_label' => 'name',
                'placeholder' => t('Choose a type'),
                'required' => true,
                'autocomplete' => true,
                'multiple' => false,
                'query_builder' => function (LevelRepository $levelRepository) {
                    return $levelRepository->createQueryBuilder('l');
                },
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
