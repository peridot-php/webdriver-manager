<?php
namespace Peridot\WebDriverManager\Binary;

use Peridot\WebDriverManager\Binary\Request\BinaryRequestInterface;
use Peridot\WebDriverManager\OS\System;
use Peridot\WebDriverManager\Versions;

class SeleniumStandalone extends AbstractBinary
{
    /**
     * @param BinaryRequestInterface $request
     */
    public function __construct(BinaryRequestInterface $request)
    {
        parent::__construct($request, new System());
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getFileName()
    {
        $version = Versions::SELENIUM;
        return "selenium-server-standalone-$version.jar";
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getUrl()
    {
        $version = Versions::SELENIUM;
        $short = substr($version, 0, strrpos($version, '.'));
        return "http://selenium-release.storage.googleapis.com/$short/{$this->getFileName()}";
    }

    /**
     * Remove old versions of the binary.
     *
     * @param $directory
     * @return void
     */
    protected function removeOldVersions($directory)
    {
        $paths = glob("$directory/selenium-server-standalone-*");
        foreach ($paths as $path) {
            unlink($path);
        }
    }
}
