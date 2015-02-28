<?php
namespace Peridot\WebDriverManager\Binary;

use Peridot\WebDriverManager\Versions;

class SeleniumStandalone extends AbstractBinary
{
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
}
