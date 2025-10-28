<?php

namespace Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Generator\XorShift;
use Validator\SequenceOfNumbers;

#[AsCommand(
    name: 'generate:codes',
    description: 'Generate pseudo-random codes.'
)]
class GenerateCommand extends Command
{
    public function __invoke(
        #[Argument(description: 'How many codes would you like to generate?')] int $number,
        #[Argument(description: 'Which seed would you like to use?')] int $seed,
        InputInterface $input, OutputInterface $output,
    ): int {
        $codeLength = 5;
        $possibleChars = 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        $possibleCharsLength = strlen($possibleChars);

        try {
            $output->writeln("Starting codes generation...");

            $timeStart = microtime(true);
            $handle = fopen(__DIR__.'/../../data/codes.csv', 'wb');

            $count = 0;
            $codes = [];
            $generator = new XorShift($seed);

            while ($count < $number) {
                $randomValue = $generator->random();

                while (strlen($randomValue) < ($codeLength * 2)) {
                    $randomValue .= random_int(0, 9);
                }

                $split = str_split($randomValue);
                $map = [
                    ($split[0].$split[1]) % $possibleCharsLength,
                    ($split[2].$split[3]) % $possibleCharsLength,
                    ($split[4].$split[5]) % $possibleCharsLength,
                    ($split[6].$split[7]) % $possibleCharsLength,
                    ($split[8].$split[9]) % $possibleCharsLength,
                ];

                if (!SequenceOfNumbers::isValid($map)) {
                    continue;
                }

                $code = $possibleChars[$map[0]].
                        $possibleChars[$map[1]].
                        $possibleChars[$map[2]].
                        $possibleChars[$map[3]].
                        $possibleChars[$map[4]];

                $codes[$code] = 0;

                if (count($codes) === $count + 1) {
                    $count++;
                    fwrite($handle, $code."\r\n");
                }
            }

            fclose($handle);

            $output->writeln("Done!");

            $timeEnd = microtime(true);
            $time = round($timeEnd - $timeStart, 1);
            $output->writeln('It took '.number_format($time, 0, ',', ' ').' seconds to write '.number_format($count, 0, ',', ' ').' generated codes to file.');

        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
