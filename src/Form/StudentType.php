<?php

namespace App\Form;

use App\Entity\ClassGroup;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class StudentType extends AbstractType
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $url = $this->router->generate('student_get_photo', ['id' => $options['userId']]);
        $builder
            ->add('username', TextType::class, [
                'label' => 'Numéro étudiant'
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('email', EmailType::class, [])
            ->add('photoFile', VichImageType::class, [
                'label' => 'Photo',
                'required' => false,
                'allow_delete' => true,
                'image_uri' => $url,
                'download_uri' => $url,
            ])
            ->add('classGroup', EntityType::class, [
                'label' => 'Classe',
                'class' => ClassGroup::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'select2'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
            'userId' => null,
        ]);
    }
}
