<?php
namespace Peridot\WebDriverManager\Binary;

use Peridot\WebDriverManager\Versions;

/**
 * ChromeDriver is used to resolve the Selenium Server driver for using the Chrome
 * web browser.
 *
 * @package Peridot\WebDriverManager\Binary
 */
class ChromeDriver extends CompressedBinary implements DriverInterface
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
        $system = $this->resolver->getSystem();

        if ($system->isMac()) {
            $file .= 'mac64';
        }

        if ($system->isWindows()) {
            $file .= "win32";
        }

        if ($system->isLinux()) {
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
     * Get the linux filename.
     *
     * @return string
     */
    public function getLinuxFileName()
    {
        $file = "linux32";
        $system = $this->resolver->getSystem();
        if ($system->is64Bit()) {
            $file = 'linux64';
        }
        return $file;
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
     * {@inheritdoc}
     *
     * @param string $directory
     * @return string
     */
    protected function getOldFilePattern($directory)
    {
        return $directory . '/' . str_replace(Versions::CHROMEDRIVER, '*', $this->getOutputFileName());
    }

    /**
     * {@inheritdoc}
     *
     * @param string $directory
     * @return string
     */
    public function getDriverPath($directory)
    {
        $file = "$directory/chromedriver";
        $system = $this->resolver->getSystem();

        if ($system->isWindows()) {
            $file .= '.exe';
        }

        return "webdriver.chrome.driver=$file";
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getExtractedName()
    {
        $name = $this->getName();
        $system = $this->resolver->getSystem();

        if ($system->isWindows()) {
            $name .= '.exe';
        }
        return $name;
    }
}
