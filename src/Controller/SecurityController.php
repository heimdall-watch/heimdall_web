<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('dashboard');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("{id}/device/{deviceId}", name="delete_device", methods={"DELETE"})
     */
    public function deleteDevice(User $user, string $deviceId)
    {
        $user->deleteDevice($deviceId);

        $this->getDoctrine()->getManager()->flush();

        if ($user instanceof Student) {
            return $this->redirectToRoute('student_show', ['id' => $user->getId()]);
        } elseif ($user instanceof Teacher) {
            return $this->redirectToRoute('teacher_show', ['id' => $user->getId()]);
        }
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        // Automatic
    }
}
