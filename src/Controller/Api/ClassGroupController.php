<?php

namespace App\Controller\Api;

use App\Entity\ClassGroup;
use App\Entity\Teacher;
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
}
