<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use mersenne_twister\twister;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate:mt')
            ->setDescription('Generate Mersenne Twister pseudo-random codes.')
            ->addArgument(
                'seed',
                InputArgument::REQUIRED,
                'Which seed number would you like to start with?'
            )
            ->addArgument(
                'number',
                InputArgument::OPTIONAL,
                'How many number would you like to generate?',
                10
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $number = $input->getArgument('number');
        $seed = $input->getArgument('seed');
    }
}
