<?php

namespace App\Command;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class HeimdallInitCommand extends Command
{
    protected static $defaultName = 'heimdall:init';
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Initialize the Heimdall server and create a super admin account.')
            ->addArgument('username', InputArgument::REQUIRED, 'The superadmin username')
            ->addArgument('email', InputArgument::REQUIRED, 'The superadmin email')
            ->addOption('firstname', null, InputOption::VALUE_OPTIONAL, 'The superadmin firstname')
            ->addOption('lastname', null, InputOption::VALUE_OPTIONAL, 'The superadmin lastname')
            ->addOption('force', null, InputOption::VALUE_NONE, 'WARNING: THIS WILL EMPTY ALL THE DATABASE!')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $helper = $this->getHelper('question');

        if ($input->getOption('force') === true) {
            $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
            foreach ($this->em->getConnection()->getSchemaManager()->listTableNames() as $tableName) {
                $this->em->getConnection()->prepare("ALTER TABLE " . $tableName . " DISABLE TRIGGER ALL;")->execute();
                $sql = 'DELETE FROM ' . $tableName;
                $this->em->getConnection()->prepare($sql)->execute();
                $this->em->getConnection()->prepare("ALTER TABLE " . $tableName . " ENABLE TRIGGER ALL;")->execute();
                $io->writeln($tableName . ' cleared.');
            }
        }
        if ($this->em->getRepository(Admin::class)->hasSuperAdmin()) {
            $io->error('A superadmin already exists, you cannot initialize the server twice. Please use --force option to empty the database (THIS ACTION IS IRREVERSIBLE)');
            return;
        }

        $question = new Question('Superadmin password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $password = $helper->ask($input, $output, $question);

        $superadmin = new Admin();
        $superadmin->setRoles(['ROLE_SUPER_ADMIN'])
            ->setUsername($username)
            ->setPlainPassword($password)
            ->setFirstname($input->getOption('firstname') ? $input->getOption('firstname') : $username)
            ->setLastname($input->getOption('lastname') ? $input->getOption('lastname') : $username)
            ->setEmail($email);
        $this->em->persist($superadmin);
        $this->em->flush();

        // TODO : init other server config?

        $io->success('The Heimdall server is initialized! You can now log in as superadmin!');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $question = new Question('Superadmin username: ');
            $question->setValidator(function($username) {
                if (empty($username)) {
                    throw new \Exception('Username cannot be empty');
                }
                return $username;
            });
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('username', $answer);
        }
        if (!$input->getArgument('email')) {
            $question = new Question('Superadmin email: ');
            $question->setValidator(function($email) {
                if (empty($email)) {
                    throw new \Exception('Email cannot be empty');
                }
                return $email;
            });
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('email', $answer);
        }
    }
}
