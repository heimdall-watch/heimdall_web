<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class HeimdallChangePasswordCommand extends Command
{
    protected static $defaultName = 'heimdall:change-password';
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    // TODO : For now, only update from the master branch. For a production use, should update considering tags.
    protected function configure()
    {
        $this
            ->setDescription('Update the password of an user, useful when debugging.')
            ->addArgument('username', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');

        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
        if ($user === null) {
            $io->error('The user ' . $username . ' does not exists.');
            return;
        }

        $helper = $this->getHelper('question');

        $question = new Question('Password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $password = $helper->ask($input, $output, $question);

        $user->setPlainPassword($password);
        $this->em->flush();

        $io->success('The user pasword has been updated!');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $question = new Question('Username: ');
            $question->setValidator(function($username) {
                if (empty($username)) {
                    throw new \Exception('Username cannot be empty');
                }
                return $username;
            });
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('username', $answer);
        }
    }
}
