<?php
namespace Peridot\WebDriverManager\Binary;


interface BinaryRequestInterface
{
    /**
     * Request the binary at the specified url, and return
     * the contents.
     *
     * @param $url
     * @return string
     */
    public function request($url);
}
