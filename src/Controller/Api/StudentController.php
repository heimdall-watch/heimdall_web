<?php

namespace App\Controller\Api;

use App\Entity\Student;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/student")
 */
class StudentController extends AbstractController
{
    /**
     * @Rest\Post("/update_password", name="student_update_password")
     *
     * @param string $oldPassword
     * @param string $newPassword
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return bool
     */
    public function updatePassword(string $oldPassword, string $newPassword, UserPasswordEncoderInterface $passwordEncoder)
    {
        if (!$passwordEncoder->isPasswordValid($this->getUser(), $oldPassword)) {
            throw $this->createAccessDeniedException();
        }
        /** @var Student $student */
        $student = $this->getUser();
        $student->setPlainPassword($newPassword);
        $this->getDoctrine()->getManager()->flush();

        return true;
    }
}