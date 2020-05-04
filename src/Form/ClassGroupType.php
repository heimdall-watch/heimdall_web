<?php

namespace App\Form;

use App\Entity\ClassGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('university', TextType::class, [
                'label' => 'Université',
            ])
            ->add('UFR', TextType::class, [
                'label' => 'UFR',
            ])
            ->add('formation', TextType::class, [
                'label' => 'Formation',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ClassGroup::class,
        ]);
    }
}
