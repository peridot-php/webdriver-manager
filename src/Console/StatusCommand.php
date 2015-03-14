<?php
namespace Peridot\WebDriverManager\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * StatusCommand shows what drivers and binaries are up to date, out of date, or not present.
 *
 * @package Peridot\WebDriverManager\Console
 */
class StatusCommand extends AbstractManagerCommand
{
    protected function configure()
    {
        $this
            ->setName('status')
            ->setDescription('List the current available drivers');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $binaries = $this->manager->getBinaries();
        foreach ($binaries as $name => $binary) {
            $this->outputBinaryStatus($output, $binary);
        }
        return 0;
    }

    /**
     * @param OutputInterface $output
     * @param $binary
     */
    protected function outputBinaryStatus(OutputInterface $output, $binary)
    {
        $directory = $this->manager->getInstallPath();
        $tag = "comment";
        if ($binary->exists($directory)) {
            $message = "{$binary->getName()} is up to date";
            $tag = "info";
        } else if ($binary->isOutOfDate($directory)) {
            $message = "{$binary->getName()} needs to be updated";
        } else {
            $message = "{$binary->getName()} is not present";
        }
        $output->writeln("<$tag>$message</$tag>");
    }
}
