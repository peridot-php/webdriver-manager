<?php
namespace Peridot\WebDriverManager\Console;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * UpdateCommand is used to update all binaries or a specific binary.
 *
 * @package Peridot\WebDriverManager\Console
 */
class UpdateCommand extends AbstractManagerCommand
{
    protected function configure()
    {
        $this
            ->setName('update')
            ->setDescription('Download or update Selenium Server and drivers')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Update a specific binary with the given name'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name') ?: '';
        $message = $this->getUpdateMessage($name);
        $output->writeln("<info>$message</info>");
        $this->update($output, $name);
        return 0;
    }

    /**
     * Track update progress.
     *
     * @param OutputInterface $output
     * @param $name
     */
    protected function update(OutputInterface $output, $name)
    {
        $progress = new ProgressBar($output);
        $progress->setFormat('%bar% (%percent%%)');
        $this->manager->on('request.start', function ($url, $bytes) use ($progress, $output) {
            $output->writeln('<comment>Downloading ' . basename($url) . '</comment>');
            $progress->start($bytes);
        });
        $this->manager->on('progress', function ($transferred) use ($progress) {
            $progress->setProgress($transferred);
        });
        $this->manager->on('complete', function () use ($progress, $output) {
            $progress->finish();
            $output->writeln('');
        });
        $this->manager->update($name);
    }

    /**
     * @param $name
     * @param $binaries
     * @return string
     */
    protected function getUpdateMessage($name)
    {
        $binaries = $this->manager->getBinaries();
        $message = 'Ensuring binaries are up to date';
        $isSingleUpdate = $name && array_key_exists($name, $binaries);

        if (! $isSingleUpdate) {
            return $message;
        }

        $binary = $binaries[$name];
        return $binary->isSupported() ? "Updating {$binary->getName()}" : "{$binary->getName()} is not supported by your system";
    }
}
