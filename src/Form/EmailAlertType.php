<?php

namespace App\Form;

use App\Entity\ClassGroup;
use App\Entity\EmailAlert;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailAlertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [])
            ->add('periodicity', ChoiceType::class, [
                'label' => 'Périodicité',
                'choices' => [
                    EmailAlert::getLabelForPeriodicity(EmailAlert::PERIO_DAILY) => EmailAlert::PERIO_DAILY,
                    EmailAlert::getLabelForPeriodicity(EmailAlert::PERIO_WEEKLY) => EmailAlert::PERIO_WEEKLY,
                    EmailAlert::getLabelForPeriodicity(EmailAlert::PERIO_MONTHLY) => EmailAlert::PERIO_MONTHLY,
                ]
            ])
            ->add('watchedClasses', EntityType::class, [
                'label' => 'Classes',
                'class' => ClassGroup::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => ['class' => 'select2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EmailAlert::class,
        ]);
    }
}
