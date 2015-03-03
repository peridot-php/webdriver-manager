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
    public function getName()
    {
        return 'chromedriver';
    }

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

        if ($this->system->isWindows()) {
            $file .= "win32";
        }

        if ($this->system->isLinux()) {
            $file .= $this->getLinuxFileName();
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

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getOutputFileName()
    {
        $version = Versions::CHROMEDRIVER;
        return "chromedriver_$version.zip";
    }

    /**
     * Remove old versions of the binary.
     *
     * @param $directory
     * @return void
     */
    protected function removeOldVersions($directory)
    {
        $paths = glob("$directory/chromedriver*");
        foreach ($paths as $path) {
            unlink($path);
        }
    }

    /**
     * Get the linux filename.
     *
     * @return string
     */
    private function getLinuxFileName()
    {
        $file = "linux32";
        if ($this->system->is64Bit()) {
            $file = 'linux64';
        }
        return $file;
    }
}
