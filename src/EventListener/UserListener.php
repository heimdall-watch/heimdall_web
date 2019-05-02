<?php


namespace App\EventListener;


use App\Entity\User;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Swift_Mailer;
use Swift_Message;

class UserListener
{
    private $mailer;

    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        /**
         * @var User $user
         */
        $user = $args->getObject();
        $message = (new Swift_Message('Inscription'))
            ->setFrom('send@example.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'email/email_creation.html.twig',
                    ['name' => $user->getUsername(),
                    'password' => $user->getPassword()]
                ),
                'text/html'
            )
        ;

        //$mailer->send($message);
    }


}