<?php
namespace Peridot\WebDriverManager\Process;

use Peridot\WebDriverManager\Binary\BinaryInterface;

/**
 * SeleniumProcessInterface describes how to control a Selenium process.
 *
 * @package Peridot\WebDriverManager\Process
 */
interface SeleniumProcessInterface
{
    /**
     * Returns whether or not java is available for use.
     *
     * @return bool
     */
    public function isAvailable();

    /**
     * Add a driver or binary to the argument list. Binary files will
     * be searched for in the given directory.
     *
     * @param BinaryInterface $binary
     * @param $directory
     * @return mixed
     */
    public function addBinary(BinaryInterface $binary, $directory);

    /**
     * Add arguments to be used by the process.
     *
     * @param string $arg - should support variadic arguments
     * @return void
     */
    public function addArg($arg);

    /**
     * Add multiple arguments at once.
     *
     * @param array $args
     * @return void
     */
    public function addArgs(array $args);

    /**
     * Return the arguments being used by the process.
     *
     * @return array
     */
    public function getArgs();

    /**
     * Start the process and return it.
     *
     * @param bool $background
     * @return $this
     */
    public function start($background);

    /**
     * Return the java command being opened by the process.
     *
     * @return string
     */
    public function getCommand();

    /**
     * Get process status. If loop is true it will loop
     * until the process has finished before returning a final
     * status.
     *
     * @param bool $loop
     * @return array
     */
    public function getStatus($loop);

    /**
     * Returns whether or not the process is running.
     *
     * @return bool
     */
    public function isRunning();

    /**
     * Get contents of error stream.
     *
     * @return string
     */
    public function getError();

    /**
     * Close the process.
     *
     * @return int
     */
    public function close();
}
