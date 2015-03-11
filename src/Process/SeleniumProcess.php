<?php
namespace Peridot\WebDriverManager\Process;

use Peridot\WebDriverManager\Binary\BinaryInterface;
use Peridot\WebDriverManager\Binary\DriverInterface;

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
        $this->addArg('-jar');
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

        $this->addArg(realpath($directory . '/' . $binary->getFileName()));
    }

    /**
     * Return the arguments used to build up this process.
     *
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Add an argument to the argument list.
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
     * @return bool
     */
    public function isAvailable()
    {
        $command = 'java -version';
        $descriptors = $this->getDescriptorSpec();
        $proc = proc_open($command, $descriptors, $pipes);
        $status = $this->getStatus($proc, true);
        return $status['exitcode'] == 0;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function start()
    {
        $command = $this->getCommand();
        $descriptorSpec = $this->getDescriptorSpec();
        $this->process = proc_open($command, $descriptorSpec, $this->pipes);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isRunning()
    {
        $status = $this->getStatus($this->process);
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
     * @return bool
     */
    public function terminate()
    {
        return proc_terminate($this->process);
    }

    /**
     * {@inheritdocs}
     *
     * @return array
     */
    public function getPipes()
    {
        return $this->pipes;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getCommand()
    {
        return 'java ' . implode(' ', $this->args);
    }

    /**
     * Run a process until it is finished, returning the
     * final status of the operation.
     *
     * @param resource $proc
     * @return array
     */
    private function getStatus($proc, $loop = false)
    {
        $status = proc_get_status($proc);
        while ($loop && $status['running']) {
            usleep(20000);
            $status = proc_get_status($proc);
        }
        return $status;
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
