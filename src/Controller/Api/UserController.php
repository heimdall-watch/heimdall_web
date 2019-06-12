<?php

namespace App\Controller\Api;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Swift_Message;
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

    public function resetPassword(User $user)
    {
        if (empty($user->getPlainPassword()) && empty($user->getPassword())) {
            $passwordGenerator = new ComputerPasswordGenerator();
            $passwordGenerator
                ->setUppercase()
                ->setLowercase()
                ->setNumbers()
                ->setSymbols(false)
                ->setLength(10);
            $user->setPlainPassword($passwordGenerator->generatePassword());
        }

        $serverName = getenv('HEIMDALL_SERVER_NAME');
        $message = (new Swift_Message('Mot de passe oublié'))
            ->setFrom('no-reply@' . $serverName, $serverName)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/email_forgot_password.html.twig',
                    [
                        'name' => $user->getUsername(),
                        'password' => $user->getPlainPassword(),
                        'type' => $user->getType(),
                    ]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);

    }
}