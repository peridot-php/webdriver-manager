<?php
namespace Peridot\WebDriverManager\Console;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $binaries = $this->manager->getBinaries();

        $message = 'Ensuring binaries are up to date';
        if ($name && array_key_exists($name, $binaries)) {
            $message = "Updating " . $binaries[$name]->getName();
        }

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
} 
