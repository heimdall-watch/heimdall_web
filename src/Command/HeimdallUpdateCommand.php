<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class HeimdallUpdateCommand extends Command
{
    protected static $defaultName = 'heimdall:update';
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
            ->setDescription('Update the Heimdall server')
            ->addOption('hard', null, InputOption::VALUE_NONE, 'Reset hard instead of git pull (any uncommitted change will be lost)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $output_callback = function ($type, $buffer) use ($io) {
            $io->write($buffer);
        };

        if ($input->getOption('hard') === true) {
            $git_pull = new Process(['git', 'fetch', '--all'], '/home/www/heimdall_web');
            $git_pull->run($output_callback);
            if (!$git_pull->isSuccessful()) {
                throw new \Exception("Git fetch -all failed: " . $git_pull->getErrorOutput());
            }

            $git_pull = new Process(['git', 'reset', '--hard', 'origin/master'], '/home/www/heimdall_web');
            $git_pull->run($output_callback);
            if (!$git_pull->isSuccessful()) {
                throw new \Exception("Git reset hard: " . $git_pull->getErrorOutput());
            }
        } else {
            $git_pull = new Process(['git', 'pull'], '/home/www/heimdall_web');
            $git_pull->run($output_callback);
            if (!$git_pull->isSuccessful()) {
                throw new \Exception("Git pull failed (to overwrite your changes, run with the --hard option): " . $git_pull->getErrorOutput());
            }
        }

        $composer_install = new Process(['composer', 'install'], '/home/www/heimdall_web');
        $composer_install->run($output_callback);
        if (!$composer_install->isSuccessful()) {
            throw new \Exception("Composer install failed: " . $composer_install->getErrorOutput());
        }

        $yarn_install = new Process(['yarn', 'install'], '/home/www/heimdall_web');
        $yarn_install->run($output_callback);
        if (!$yarn_install->isSuccessful()) {
            throw new \Exception("Yarn install failed: " . $yarn_install->getErrorOutput());
        }

        $yarn_run = new Process(['yarn', 'run', 'encore', getenv('APP_ENV')], '/home/www/heimdall_web');
        $yarn_run->run($output_callback);
        if (!$yarn_run->isSuccessful()) {
            throw new \Exception("Yarn run failed: " . $yarn_run->getErrorOutput());
        }

        $doctrine_update = $this->getApplication()->find('doctrine:schema:update');
        $doctrine_update->run(new ArrayInput(['--force' => true]), $output);

        $doctrine_update = $this->getApplication()->find('cache:clear');
        $doctrine_update->run(new ArrayInput([]), $output);

        $io->success('The Heimdall server has been updated!');
    }
}
