<?php
namespace Peridot\WebDriverManager\Binary;

class ChromeDriver extends CompressedBinary
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getFileName()
    {
        return "chromedriver_mac32.zip";
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getUrl()
    {
        return "http://chromedriver.storage.googleapis.com/2.14/{$this->getFileName()}";
    }
}
