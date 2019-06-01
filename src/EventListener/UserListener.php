<?php


namespace App\EventListener;


use App\Entity\User;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;

class UserListener
{
    private $mailer;
    private $twig;
    private $passwordEncoder;

    public function __construct(Swift_Mailer $mailer, Environment $twig, UserPasswordEncoderInterface $passwordEncode)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->passwordEncoder = $passwordEncode;
    }

    public function prePersist(User $user)
    {
        $message = (new Swift_Message('Inscription'))
            ->setFrom('send@example.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/email_creation.html.twig',
                    ['name' => $user->getUsername(),
                    'password' => $user->getPlainPassword()]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }

    public function preFlush(User $user)
    {
        // If the user has a plainPassword, hash it and save it
        if (!empty($user->getPlainPassword())) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
        // If the user don't have password, generate and hash one
        } elseif (empty($user->getPassword())) {
            $passwordGenerator = new ComputerPasswordGenerator();
            $passwordGenerator
                ->setUppercase()
                ->setLowercase()
                ->setNumbers()
                ->setSymbols(false)
                ->setLength(10);
            $password = $passwordGenerator->generatePassword();
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        }
    }

}