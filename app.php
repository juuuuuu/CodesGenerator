<?php

require __DIR__.'/vendor/autoload.php';

use Command\GenerateCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new GenerateCommand());
$application->run();
