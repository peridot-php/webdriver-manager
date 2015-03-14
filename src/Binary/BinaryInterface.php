<?php
namespace Peridot\WebDriverManager\Binary;

/**
 * BinaryInterface describes an interface for getting binary
 * information and fetching binaries.
 *
 * @package Peridot\WebDriverManager\Binary
 */
interface BinaryInterface
{
    /**
     * Return a unique name for the binary.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the name of the binary.
     *
     * @return string
     */
    public function getFileName();

    /**
     * Get the remote location of the binary.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Fetch the contents of the url and store
     * the contents.
     *
     * @return bool
     */
    public function fetch();

    /**
     * Write the contents of the fetched url.
     *
     * @param string $directory
     * @return bool
     */
    public function save($directory);

    /**
     * Fetch and save the binary.
     *
     * @param string $directory
     * @return bool
     */
    public function fetchAndSave($directory);

    /**
     * Return the fetched content of a binary.
     *
     * @return string
     */
    public function getContents();

    /**
     * Check if the binary exists in the specified directory.
     *
     * @param $directory
     * @return bool
     */
    public function exists($directory);

    /**
     * Check if old versions of the binary exist.
     *
     * @param $directory
     * @return bool
     */
    public function isOutOfDate($directory);

    /**
     * Is the binary supported by the target system?
     *
     * @return bool
     */
    public function isSupported();
} 
