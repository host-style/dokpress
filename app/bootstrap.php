<?php

use Symfony\Component\Dotenv\Dotenv;

require_once WD_BASE_PATH .'/vendor/autoload.php';

// Load environment variables from .env file
$dotenv = new Dotenv();
$envPath = WD_BASE_PATH .'/.env';

if(file_exists($envPath)) $dotenv->load($envPath);
