<?php
namespace Peridot\WebDriverManager\Binary;

use splitbrain\PHPArchive\Zip;

class ChromeDriver extends AbstractBinary
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

    /**
     * Saves zipped contents and unpacks them to destination.
     *
     * @param string $output
     * @return bool
     */
    public function save($directory)
    {
        $zipPath = tempnam(sys_get_temp_dir(), "WDM_");
        file_put_contents($zipPath, $this->contents);
        $zip = new Zip();
        $zip->open($zipPath);
        $info = $zip->extract($directory);
        return !empty($info);
    }
}
