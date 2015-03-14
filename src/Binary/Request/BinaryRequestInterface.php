<?php
namespace Peridot\WebDriverManager\Binary\Request;

use Peridot\WebDriverManager\Event\EventEmitterInterface;

/**
 * BinaryRequestInterface describes how to fetch a remote binary.
 *
 * @package Peridot\WebDriverManager\Binary\Request
 */
interface BinaryRequestInterface extends EventEmitterInterface
{
    /**
     * Request the binary at the specified url, and return
     * the contents.
     *
     * @param string $url
     * @return string
     */
    public function request($url);
}
