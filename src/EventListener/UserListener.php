<?php


namespace App\EventListener;


use App\Entity\User;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
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
        if(!empty($user->getPlainPassword()))
        {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));

        }

    }

}