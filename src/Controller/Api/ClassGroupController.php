<?php

namespace App\Controller\Api;

use App\Entity\ClassGroup;
use App\Entity\Student;
use App\Entity\Teacher;
use Doctrine\Common\Collections\Collection;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ClassGroupController
 * @package App\Controller\Api
 *
 * @Route("/class")
 */
class ClassGroupController extends AbstractController
{
    /**
     * @Rest\Get("/", name="class_get_all")
     * @Rest\View(serializerGroups={"GetClass"}, serializerEnableMaxDepthChecks=true)
     * @IsGranted("ROLE_TEACHER")
     *
     * @return ClassGroup[]
     */
    public function getClasses()
    {
        /** @var Teacher $teacher */
        $teacher = $this->getUser();
        return $teacher->getClassGroups();
    }

    /**
     * @Rest\Get("/{classGroup}/students", name="class_get_students")
     * @Rest\View(serializerGroups={"GetClassStudents"}, serializerEnableMaxDepthChecks=true)
     * @IsGranted("ROLE_TEACHER")
     *
     * @param ClassGroup $classGroup
     * @return Student[]|Collection
     */
    public function getClassStudents(ClassGroup $classGroup)
    {
        $students = $classGroup->getStudents();

        return $students;
    }
}
