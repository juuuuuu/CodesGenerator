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
            ->setName('generate:codes')
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
        $codes = array();
        try {
            $timeStart = microtime(true);

            $output->writeln("Starting code generation...");

            $seed = $input->getArgument('seed');
            $generator = new XorShift($seed);

            $number = $input->getArgument('number');

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

                if (!SequenceOfNumbers::isValid($map)) {
                    continue;
                }

                $code = $this->possibleChars[$map[0]].
                        $this->possibleChars[$map[1]].
                        $this->possibleChars[$map[2]].
                        $this->possibleChars[$map[3]].
                        $this->possibleChars[$map[4]];


                $codes[$code] = 0;
                if (count($codes) == $count+1) {
                    $count++;
                    fwrite($handle, $code."\r\n");
                }
            }

            fclose($handle);

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
