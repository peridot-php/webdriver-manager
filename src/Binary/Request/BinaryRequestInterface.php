<?php
namespace Peridot\WebDriverManager\Binary\Request;


interface BinaryRequestInterface
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
