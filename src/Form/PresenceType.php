<?php

namespace App\Form;

use App\Entity\Presence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_student', TextType::class, [
                'label' => 'Etudiant',
            ])
            ->add('id_lesson', TextType::class, [
                'label' => 'Cours',
            ])
            ->add('present', TextType::class, [
                'label' => 'Cours',
            ])
            ->add('id_lesson', TextType::class, [
                'label' => 'Cours',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Presence::class,
        ]);
    }
}
