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


        #--------------------------------------------
        // $seed is a seed for initialising the random-number generator.
        $twister1 = new twister($seed);
        $twister2 = new twister($seed);

        for($i = 0; $i < 10; $i++) {
          # int32 returns a random 32-bit integer
            if($twister1->int32() !== $twister2->int32()) {
                $output->writeln("They're different -- this is not supposed to happen!");
            }
        }

        #--------------------------------------------

        $num_iters = 1000;

        $twister3 = new twister($seed);
        $saved = serialize($twister3);

        $sum = 0;
        for($i = 0; $i < $num_iters; $i++) {
            $sum += $twister3->rangereal_halfopen(10, 20);
        }

        /*
          the call to rangereal_halfopen produces a
          floating-point number >= 10 and < 20
        */

        $output->writeln("This is the average, which should be about 15: ".($sum / $num_iters));

        $twister3 = unserialize($saved);

        # run the loop again
        #
        $sum = 0;
        for($i = 0; $i < $num_iters; $i++) {
            $sum += $twister3->rangereal_halfopen(10, 20);
        }

        $output->writeln("This is the average again, which should be the same as before: ".($sum / $num_iters));
        #--------------------------------------------

        $twister4 = new twister;

        $twister4->init_with_file("/dev/urandom", twister::N);
        /*
          This reads characters from /dev/urandom and
          uses them to initialise the random number
          generator.

          The second argument is multiplied by 4 and
          then used as an upper bound on the number of
          characters to read.
        */

        if($twister4->rangeint(1, 6) == 6) {
            $output->writeln("You've won -- congratulations!");
        }
    }
}
