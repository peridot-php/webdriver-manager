<?php
namespace Peridot\WebDriverManager\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanCommand extends AbstractManagerCommand
{
    protected function configure()
    {
        $this
            ->setName('clean')
            ->setDescription('Delete contents of installation directory');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Deleting installed binaries...</info>');
        $this->manager->clean();
        $output->writeln('<info>Installtion directory clean</info>');
        return 0;
    }
} 
