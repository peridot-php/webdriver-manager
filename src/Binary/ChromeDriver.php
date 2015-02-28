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
        $file = "chromedriver_";

        if ($this->system->isMac()) {
            $file .= 'mac32';
        }

        return "$file.zip";
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
