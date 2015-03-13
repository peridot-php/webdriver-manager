<?php
namespace Peridot\WebDriverManager\Console;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends AbstractManagerCommand
{
    protected function configure()
    {
        $this
            ->setName('start')
            ->setDescription('Start Selenium Server')
            ->addArgument(
                'port',
                InputArgument::OPTIONAL,
                'The port you want to start Selenium Server on'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = $input->getArgument('port');
        if (! $port) {
            $port = 4444;
        }

        $update = $this->getApplication()->find('update');
        $update->run(new ArrayInput(['command' => 'update']), $output);

        $output->writeln('<info>Starting Selenium Server...</info>');
        $this->manager->start(false, $port);
        return 0;
    }
} 
