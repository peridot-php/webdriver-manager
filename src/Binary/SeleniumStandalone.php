<?php
namespace Peridot\WebDriverManager\Binary;

class SeleniumStandalone extends AbstractBinary
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getFileName()
    {
        return 'selenium-server-standalone-2.44.0.jar';
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getUrl()
    {
        return "http://selenium-release.storage.googleapis.com/2.44/{$this->getFileName()}";
    }
}
