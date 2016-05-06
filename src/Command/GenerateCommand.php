<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use mersenne_twister\twister;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate:number')
            ->setDescription('Generate pseudo-random codes.')
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

        try {
            $memcache = new \Memcached();
            $memcache->addServer('localhost', 11211);

            $length = 7;
            $possibleChars = 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
            $possibleCharsLength = strlen($possibleChars);
            $previous = 0;
            $generator = 13 ** 11;
            $modulus = 7 ** 19;

            for ($i = 0; $i < $number; ++$i) {
                $previous = ($previous + $generator) % $modulus;
                $code = '';
                $temp = $previous;

                for ($j = 0; $j < $length; ++$j) {
                    $code .= $possibleChars[$temp % $possibleCharsLength];
                    $temp = $temp / $possibleCharsLength;
                }

                if (!$memcache->get($code)) {
                    $memcache->set($code, $code);

                    // @TODO: save the generated code somewhere (not a Database!)
                    $output->writeln($code);
                }

            }

            $memcache->flush();
            $memcache->quit();

        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));

            return;
        }

    }
}
