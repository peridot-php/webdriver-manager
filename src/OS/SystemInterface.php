<?php
namespace Peridot\WebDriverManager\OS;

/**
 * SystemInterface determines information about the operating system.
 *
 * @package Peridot\WebDriverManager\OS
 */
interface SystemInterface
{
    /**
     * Is the system a mac operating system?
     *
     * @return bool
     */
    public function isMac();

    /**
     * Is the system a windows operating system?
     *
     * @return bool
     */
    public function isWindows();

    /**
     * Is the system a linux system? Assumes that if the OS is not windows, and is
     * not mac, then it is linux.
     *
     * @return bool
     */
    public function isLinux();

    /**
     * Return whether or not the OS is a 64 bit operating system.
     *
     * @return bool
     */
    public function is64Bit();
}
