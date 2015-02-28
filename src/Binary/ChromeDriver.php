<?php
namespace Peridot\WebDriverManager\Binary;

use Peridot\WebDriverManager\Versions;

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
        $version = Versions::CHROMEDRIVER;
        return "http://chromedriver.storage.googleapis.com/$version/{$this->getFileName()}";
    }
}
