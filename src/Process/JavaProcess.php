<?php
namespace Peridot\WebDriverManager\Process;

class JavaProcess implements JavaProcessInterface
{
    /**
     * @var array
     */
    protected $args = [];

    public function __construct()
    {
        $this->args[] = '-jar';
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
