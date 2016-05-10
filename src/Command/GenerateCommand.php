<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Generator\XorShift;
use Validator\SequenceOfNumbers;

class GenerateCommand extends Command
{
    private $codeLength, $possibleChars, $possibleCharsLength;

    public function __construct()
    {
        parent::__construct();

        $this->codeLength = 5;
        $this->possibleChars = 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        $this->possibleCharsLength = strlen($this->possibleChars);
    }

    protected function configure()
    {
        $this
            ->setName('generate:code')
            ->setDescription('Generate pseudo-random codes.')
            ->addArgument(
                'number',
                InputArgument::REQUIRED,
                'How many code would you like to generate?'
            )
            ->addArgument(
                'seed',
                InputArgument::REQUIRED,
                'Which seed would you like to use?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $timeStart = microtime(true);

            $output->writeln("Starting code generation...");

            $seed = $input->getArgument('seed');
            $generator = new XorShift($seed);

            $number = $input->getArgument('number');

            $memcached = new \Memcached();
            $memcached->addServer('127.0.0.1', 11211);

            $handle = fopen(__DIR__.'/../../data/codes.csv', 'w');

            $count = 0;

            while ($count < $number) {
                $randomValue = $generator->random();

                while (strlen($randomValue) < ($this->codeLength * 2)) {
                    $randomValue = $randomValue.mt_rand(0, 9);
                }

                $split = str_split($randomValue);
                $map = [
                    ($split[0].$split[1]) % $this->possibleCharsLength,
                    ($split[2].$split[3]) % $this->possibleCharsLength,
                    ($split[4].$split[5]) % $this->possibleCharsLength,
                    ($split[6].$split[7]) % $this->possibleCharsLength,
                    ($split[8].$split[9]) % $this->possibleCharsLength,
                ];

                $code = $this->possibleChars[$map[0]].
                        $this->possibleChars[$map[1]].
                        $this->possibleChars[$map[2]].
                        $this->possibleChars[$map[3]].
                        $this->possibleChars[$map[4]];

                if (!SequenceOfNumbers::isValid($code)) {
                    continue;
                }

                if ($memcached->set('KEY_'.$code, $code)) {
                    if (fwrite($handle, $code."\r\n")) {;
                        $count++;
                    } else {
                        throw new \Exception("An error occured while write a code ($code) to file.", 1);
                    }
                }
            }

            fclose($handle);

            $memcached->flush();
            $memcached->quit();

            $output->writeln("Done!");

            $timeEnd = microtime(true);
            $time = round($timeEnd - $timeStart, 1);
            $output->writeln("It took $time seconds to write $count generated codes to file.");

        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));

            return;
        }
    }
}
