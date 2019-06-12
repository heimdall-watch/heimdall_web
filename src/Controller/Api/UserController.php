<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

abstract class UserController extends AbstractController
{
    public function updatePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $teacher = $this->getUser();
        if (!$passwordEncoder->isPasswordValid($teacher, $request->request->get('oldPassword'))) {
            throw new HttpException(403, "Mot de passe actuel incorrect");
        }
        $teacher->setPlainPassword($request->request->get('newPassword'));
        $this->getDoctrine()->getManager()->flush();

        return true;
    }
}