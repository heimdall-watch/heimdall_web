<?php

namespace App\Form;

use App\Entity\StudentPresence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class StudentPresenceImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('photoFile', VichImageType::class, [
                'required' => true,
            ])
            ->add('excuse', TextType::class,['required' => true,])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StudentPresence::class,
            'csrf_protection' => false,
        ]);
    }

}
