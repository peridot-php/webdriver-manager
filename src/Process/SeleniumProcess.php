<?php
namespace Peridot\WebDriverManager\Process;

use Peridot\WebDriverManager\Binary\BinaryInterface;
use Peridot\WebDriverManager\Binary\DriverInterface;

/**
 * SeleniumProcess is responsible for controlling Selenium Server processes.
 *
 * @package Peridot\WebDriverManager\Process
 */
class SeleniumProcess implements SeleniumProcessInterface
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var resource
     */
    protected $process = null;

    /**
     * @var array
     */
    protected $pipes = [];

    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param BinaryInterface $binary
     * @param string $directory
     * @return void
     */
    public function addBinary(BinaryInterface $binary, $directory)
    {
        if (! $binary->exists($directory)) {
            return;
        }

        if ($binary instanceof DriverInterface) {
            $this->addArg('-D' . $binary->getDriverPath($directory));
            return;
        }

        if(strpos($binary->getFileName(), "selenium-server-standalone") !== false){
            $this->addArg('-jar');
        }

        $this->addArg(realpath($directory . '/' . $binary->getFileName()));
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $arg
     * @return void
     */
    public function addArg($arg)
    {
        $this->args[] = $arg;
        $rest = array_slice(func_get_args(), 1);
        foreach ($rest as $arg) {
            $this->args[] = $arg;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param array $args
     * @return void
     */
    public function addArgs(array $args)
    {
        foreach ($args as $arg) {
            $this->args[] = $arg;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isAvailable()
    {
        $command = 'java -version';
        $descriptors = $this->getDescriptorSpec();
        $this->process = proc_open($this->formatCommand($command), $descriptors, $this->pipes);
        $status = $this->getStatus(true);
        $available = $status['exitcode'] == 0;
        proc_close($this->process);
        return $available;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function start($background = false)
    {
        $command = $this->getCommand();
        if (! $background) {
            exec($command);
            return $this;
        }

        $descriptors = $this->getDescriptorSpec();
        $this->process = proc_open($command, $descriptors, $this->pipes);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->formatCommand('java ' . implode(' ', $this->args));
    }

    /**
     * {@inheritdoc}
     *
     * @param bool $loop
     * @return array
     */
    public function getStatus($loop = false)
    {
        $status = proc_get_status($this->process);
        while ($loop && $status['running']) {
            usleep(20000);
            $status = proc_get_status($this->process);
        }
        return $status;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isRunning()
    {
        $status = $this->getStatus();
        return $status['running'];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getError()
    {
        return stream_get_contents($this->pipes[2]);
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function close()
    {
        proc_terminate($this->process);
        return proc_close($this->process);
    }

    /**
     * Format a shell command according to the running Operating System to avoid zombie processes.
     *
     * @param string $command A shell command.
     * @return string
     */
    private function formatCommand($command)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $command;
        }
        return 'exec ' . $command;
    }

    /**
     * Helper to create an array suitable as a descriptor for process streams.
     *
     * @return array
     */
    private function getDescriptorSpec()
    {
        return [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];
    }
}
