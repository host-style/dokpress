<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSalts extends Command
{
    private string $envFile;

    public function __construct()
    {
        parent::__construct();
        $this->envFile = WD_BASE_PATH . '/.env';
    }

    protected function configure(): void
    {
        $this
            ->setName('dokpress:update-salts')
            ->setDescription('Update WordPress SALT keys in .env file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $salts = @file_get_contents('https://api.wordpress.org/secret-key/1.1/salt/');
        if (!$salts) {
            $output->writeln('<error>Failed to fetch SALT keys from WordPress API.</error>');
            return Command::FAILURE;
        }

        if (!file_exists($this->envFile)) {
            $output->writeln("<error>.env file not found at {$this->envFile}</error>");
            return Command::FAILURE;
        }

        $envContent = file_get_contents($this->envFile);

        $patternMap = [
            'AUTH_KEY',
            'SECURE_AUTH_KEY',
            'LOGGED_IN_KEY',
            'NONCE_KEY',
            'AUTH_SALT',
            'SECURE_AUTH_SALT',
            'LOGGED_IN_SALT',
            'NONCE_SALT',
        ];

        foreach ($patternMap as $key) {
            if (preg_match("/define\(\s*'{$key}'\s*,\s*'(.+?)'\s*\);/", $salts, $match)) {
                $value = $match[1];
                if (preg_match("/^{$key}=.*$/m", $envContent)) {
                    $envContent = preg_replace("/^{$key}=.*$/m", "{$key}=\"{$value}\"", $envContent);
                } else {
                    $envContent .= "\n{$key}=\"{$value}\"";
                }
            }
        }

        file_put_contents($this->envFile, $envContent);
        $output->writeln('<info>Salt keys updated successfully in .env file</info>');

        return Command::SUCCESS;
    }
}
