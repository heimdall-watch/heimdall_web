<?php


namespace App\EventListener;


use App\Entity\User;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class UserListener
{
    private $mailer;
    private $twig;

    public function __construct(Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function postPersist(User $user)
    {
        $message = (new Swift_Message('Inscription'))
            ->setFrom('send@example.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/email_creation.html.twig',
                    ['name' => $user->getUsername(),
                    'password' => $user->getPassword()]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }


}