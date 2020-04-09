<?php

namespace App\Form;

use App\Entity\ClassGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Range;

class TeacherImportType extends AbstractType
{
    const IMPORT_MIME_TYPES = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/plain', 'text/csv', 'application/vnd.ms-excel', 'application/vnd.oasis.opendocument.spreadsheet'];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'constraints' => new File(['mimeTypes' => self::IMPORT_MIME_TYPES]),
                'required' => true,
            ])
            ->add('classGroup', EntityType::class, [
                'class' => ClassGroup::class,
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('firstLineIsHeaders', CheckboxType::class, [
                'label' => 'First line is headers',
                'required' => false,
            ]);

        $headerPos = 1;
        $headerPosAttr = ['min' => 1];
        $headerPosConstraint = new Range($headerPosAttr);

        foreach (['username', 'firstname', 'lastname', 'email'] as $teacherField) {
            $builder->add($teacherField, IntegerType::class, [
                    'required' => true,
                    'attr' => $headerPosAttr,
                    'constraints' => $headerPosConstraint,
                    'data' => $headerPos++,
                ]
            );
        }
    }
}
