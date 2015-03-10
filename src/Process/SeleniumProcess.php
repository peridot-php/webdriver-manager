<?php
namespace Peridot\WebDriverManager\Process;

use Peridot\WebDriverManager\Binary\BinaryInterface;
use Peridot\WebDriverManager\Binary\DriverInterface;

class SeleniumProcess implements JavaProcessInterface
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
     * Add a driver to the argument list. Driver files will
     * be searched for in the given directory.
     *
     * @param BinaryInterface $binary
     * @param string $directory
     * @return void
     */
    public function addDriver(BinaryInterface $binary, $directory)
    {
        if (! $binary->exists($directory)) {
            return;
        }

        if ($binary instanceof DriverInterface) {
            $this->addArg('-D' . $binary->getDriverPath($directory));
        }
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
