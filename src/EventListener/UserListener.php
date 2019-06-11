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
        // If the user don't have password, generate one
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
        $message = (new Swift_Message('Inscription'))
            ->setFrom('no-reply@' . $serverName, $serverName)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/email_creation.html.twig',
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

    public function preFlush(User $user)
    {
        // If the user has a plainPassword, hash it and save it
        if (!empty($user->getPlainPassword())) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
        }
    }

}