<?php

namespace App\Command;

use Swift_Mailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;

class HeimdallSendRecapCommand extends Command
{
    protected static $defaultName = 'heimdall:send-recap';
    private $mailer;
    private $twig;


    public function __construct(Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        parent::___construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Recap to send every month')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

//        $studentPresence[] =
        $serverName = getenv('HEIMDALL_SERVER_NAME');
        $message = (new Swift_Message('Récapitulatif Absences'))
            ->setFrom('no-reply@' . $serverName, $serverName)
            ->setTo("cfa@gmail.com")//addr du cfa
            ->setBody(
                $this->twig->render(
                    'email/email_recap_cfa.html.twig',
                    [


                    ]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}
