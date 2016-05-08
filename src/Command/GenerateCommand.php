<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Generator\XorShift;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate:number')
            ->setDescription('Generate pseudo-random codes.')
            ->addArgument(
                'seed',
                InputArgument::OPTIONAL,
                'Which seed would you like to use?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $memcached = new \Memcached();
            $memcached->addServer('localhost', 11211);

            $seed = $input->getArgument('seed');
            $generator = new XorShift($seed);

            $codeLength = 5;
            $possibleChars = 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789';

            for ($i = 0; $i < 10; ++$i) {
                $randomValue = $generator->random();

                while (strlen($randomValue) < ($codeLength * 2)) {
                    $randomValue = $randomValue.mt_rand(0, 9);
                }

                $split = str_split($randomValue);

                $map = [
                    ($split[0].$split[1]) % 34,
                    ($split[2].$split[3]) % 34,
                    ($split[4].$split[5]) % 34,
                    ($split[6].$split[7]) % 34,
                    ($split[8].$split[9]) % 34,
                ];

                $code = $possibleChars[$map[0]].
                        $possibleChars[$map[1]].
                        $possibleChars[$map[2]].
                        $possibleChars[$map[3]].
                        $possibleChars[$map[4]];

                $output->writeln($randomValue." ".$code);

                // if (!$memcached->get($code)) {
                //     $memcached->set($code, $code);

                //     // @TODO: save the generated code somewhere (not a Database!)
                //     $output->writeln($code);
                // }

            }

            $memcached->flush();
            $memcached->quit();

        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));

            return;
        }
    }

    private function randXorshift($number, $x, $y, $z)
    {
        $t = ( $x ^ ($x << 11) ) & 0x7fffffff;

        $x = $y;
        $y = $z;
        $z = $number;

        $number = ( $number ^ ($number >> 19) ^ ( $t ^ ( $t >> 8 )) );

        return $number;
    }
}
