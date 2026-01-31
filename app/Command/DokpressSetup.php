<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DokpressSetup extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('dokpress:setup')
            ->setDescription('Run project setup commands');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Starting project setup...');

        $commands = [
            'Copying project files' => 'dokpress:copy-config-files',
            //'Updating Salts'        => 'dokpress:update-salts',
            'Set Wordpress config'  => 'dokpress:wordpress-deploy',
        ];

        $io->progressStart(count($commands));

        foreach ($commands as $title => $commandName) {
            $io->newLine();
            $io->section($title);

            $command = $this->getApplication()->find($commandName);

            $result = $command->run(new ArrayInput([]), $output);

            if ($result !== Command::SUCCESS) {
                $output->writeln('<error>Error:</error> ' . $commandName);
                return Command::FAILURE;
            }

            $io->progressAdvance();
            $io->newLine();
        }

        $io->progressFinish();
        $io->success('Setup completed successfully!');
        return Command::SUCCESS;
    }
}
