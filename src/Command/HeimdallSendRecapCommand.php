<?php

namespace App\Command;

use App\Entity\ClassGroup;
use App\Entity\EmailAlert;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

class HeimdallSendRecapCommand extends Command
{
    protected static $defaultName = 'heimdall:send-recap';
    private $mailer;
    private $twig;
    private $em;

    public function __construct(Swift_Mailer $mailer, Environment $twig, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Recap to send every month');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        /** @var EmailAlert[] $alerts */
        $alerts = $this->em->getRepository(EmailAlert::class)->getShouldSend();
        $today = new \DateTime();
        $tomorrow = (clone $today)->modify('+ 1 day');

        $nbSent = 0;
        foreach ($alerts as $alert) {
            if ($alert->getLastSent() === null) {
                if ($alert->getPeriodicity() === EmailAlert::PERIO_MONTHLY && $tomorrow->format('m') != $today->format('m')) {
                    continue;
                }
            }
            $io->writeln('Sending alert for ' . $alert->getEmail());


            $serverName = getenv('HEIMDALL_SERVER_NAME');
            $message = (new Swift_Message('Récapitulatif Absences'))
                ->setFrom('no-reply@' . $serverName, $serverName)
                ->setTo($alert->getEmail())
                ->setBody(
                    $this->twig->render(
                        'email/email_recap_cfa.html.twig',
                        []
                    ),
                    'text/html'
                );;

            foreach ($alert->getWatchedClasses() as $classGroup) {
                $csv = $this->getCsvForClass($classGroup);
                if ($csv !== null) {
                    $message->attach(new \Swift_Attachment($csv, $classGroup->getName() . ' ' . $today->format('Y-m-d'), 'text/csv'));
                }
            }
            $this->mailer->send($message);

            $alert->setLastSent($today);
            $this->em->flush();
            $nbSent++;
        }

        $io->success($nbSent . ' emails sent.');
    }
}
