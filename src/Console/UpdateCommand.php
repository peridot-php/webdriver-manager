<?php
namespace Peridot\WebDriverManager\Console;

use Peridot\WebDriverManager\Binary\BinaryInterface;
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
        $this->update($output, $name);
        return 0;
    }

    /**
     *
     *
     * @param OutputInterface $output
     * @param $name
     * @return void
     */
    protected function update(OutputInterface $output, $name)
    {
        $output->writeln("<info>{$this->getPreMessage($name)}</info>");
        $this->watchProgress($output);
        $pending = $this->manager->getPendingBinaries();

        $this->manager->update($name);

        $output->writeln("<info>{$this->getPostMessage($pending, $name)}</info>");
    }

    /**
     * Watch for update progress and advance a progress bar.
     *
     * @param OutputInterface $output
     * @return void
     */
    protected function watchProgress(OutputInterface $output)
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
    }

    /**
     * Get the message to initially display to the user.
     *
     * @param string $name
     * @return string
     */
    protected function getPreMessage($name)
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

    /**
     * Get the result message.
     *
     * @param array $pending
     * @param string $name
     * @return string
     */
    protected function getPostMessage(array $pending, $name)
    {
        if ($name) {
            $pending = array_filter($pending, function (BinaryInterface $binary) use ($name) {
                return $binary->getName() === $name;
            });
        }

        $count = array_reduce($pending, function ($r, BinaryInterface $binary) {
            if ($binary->exists($this->manager->getInstallPath())) {
                $r++;
            }
            return $r;
        }, 0);

        return $this->getResultString($count);
    }

    /**
     * Given a count, return an appropriate label.
     *
     * @param $count
     * @return string
     */
    protected function getResultString($count)
    {
        if ($count == 0) {
            return 'Nothing to update';
        }

        $label = $count > 1 ? 'binaries' : 'binary';

        return "$count $label updated";
    }
}
