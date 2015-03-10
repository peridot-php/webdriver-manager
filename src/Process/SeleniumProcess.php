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
        $status = $this->getStatus($proc);
        return $status['exitcode'] == 0;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function start()
    {
        // TODO: Implement start() method.
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
    private function getStatus($proc)
    {
        $status = proc_get_status($proc);
        while ($status['running']) {
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
            ['pipe' => 'r'],
            ['pipe' => 'w'],
            ['pipe' => 'w']
        ];
    }
}
