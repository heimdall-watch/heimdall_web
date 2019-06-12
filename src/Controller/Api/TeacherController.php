<?php

namespace App\Controller\Api;

use App\Entity\Teacher;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/teacher")
 * @IsGranted("ROLE_TEACHER")
 */
class TeacherController extends AbstractController
{
    /**
     * @Rest\Post("/update_password", name="teacher_update_password")
     *
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return bool
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        /** @var Teacher $teacher */
        $teacher = $this->getUser();
        if (!$passwordEncoder->isPasswordValid($teacher, $request->request->get('oldPassword'))) {
            throw new HttpException(403, "Mot de passe actuel incorrect");
        }
        $teacher->setPlainPassword($request->request->get('newPassword'));
        $this->getDoctrine()->getManager()->flush();

        return true;
    }
}