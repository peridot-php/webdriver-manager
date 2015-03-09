<?php
namespace Peridot\WebDriverManager\Binary;

/**
 * Interface DriverInterface represents a binary that is a supported driver
 * for selenium server.
 *
 * @package Peridot\WebDriverManager\Binary
 */
interface DriverInterface
{
    /**
     * Return the driver path that will be used by selenium server.
     *
     * @param string $directory
     * @return string
     */
    public function getDriverPath($directory);
} 
