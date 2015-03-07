<?php
namespace Peridot\WebDriverManager\Binary;

use Peridot\WebDriverManager\Binary\Decompression\BinaryDecompressorInterface;
use Peridot\WebDriverManager\Binary\Request\BinaryRequestInterface;
use Peridot\WebDriverManager\Binary\Decompression\ZipDecompressor;
use Peridot\WebDriverManager\OS\SystemInterface;
use Peridot\WebDriverManager\Binary\Request\StandardBinaryRequest;
use Peridot\WebDriverManager\OS\System;


/**
 * BinaryResolverInterface is responsible for fetching and decompressing binaries. It is used
 * to get a usable result given a URI.
 *
 * @package Peridot\WebDriverManager\Binary
 */
interface BinaryResolverInterface
{
    /**
     * Get the BinaryRequestInterface responsible for fetching remote binaries.
     *
     * @return BinaryRequestInterface|StandardBinaryRequest
     */
    public function getBinaryRequest();

    /**
     * Get the BinaryDecompressorInterface responsible for decompressing
     * compressed binaries.
     *
     * @return BinaryDecompressorInterface|ZipDecompressor
     */
    public function getBinaryDecompressor();

    /**
     * Get the System object responsible for determining operating system
     * information.
     *
     * @return System|SystemInterface
     */
    public function getSystem();

    /**
     * Extract a compressed file to the given directory.
     *
     * @param string $compressedFilePath
     * @param string $directory
     * @return bool
     */
    public function extract($compressedFilePath, $directory);

    /**
     * Request the binary at the specified url, and return
     * the contents.
     *
     * @param string $url
     * @return string
     */
    public function request($url);

    /**
     * Get the path that binaries are installed to.
     *
     * @return string
     */
    public function getInstallPath();
}
