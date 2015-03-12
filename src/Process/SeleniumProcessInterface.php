<?php
namespace Peridot\WebDriverManager\Process;

use Peridot\WebDriverManager\Binary\BinaryInterface;

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
     * Return the arguments being used by the process.
     *
     * @return array
     */
    public function getArgs();

    /**
     * Start the process and return it.
     *
     * @return $this
     */
    public function start();

    /**
     * Return the java command being opened by the process.
     *
     * @return string
     */
    public function getCommand();
} 
