<?php

namespace App\Controller\Api;

use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/teacher")
 * @IsGranted("ROLE_TEACHER")
 */
class TeacherController extends UserController
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
        return parent::updatePassword($request, $passwordEncoder);
    }

    /**
     * @Rest\Post("/reset_password", name="teacher_reset_password")
     *
     * @param User $user
     *
     */
    public function resetPassword(User $user)
    {
        return parent::resetPassword($user);
    }

}