<?php
namespace Peridot\WebDriverManager\Binary;

use Peridot\WebDriverManager\Versions;

/**
 * IEDriver is used to resolve the Selenium Server driver for using the Internet Explorer
 * web browser.
 *
 * @package Peridot\WebDriverManager\Binary
 */
class IEDriver extends CompressedBinary implements DriverInterface
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'IEDriver';
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getFileName()
    {
        if ($this->isSupported()) {
            $file = "IEDriverServer_Win32_";

            if ($this->resolver->getSystem()->is64Bit()) {
                $file = 'IEDriverServer_x64_';
            }

            return $file . Versions::IEDRIVER . '.zip';
        }
       return "";
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getUrl()
    {
        $fileName = $this->getFileName();

        if (!$fileName) {
            return '';
        }

        $version = Versions::IEDRIVER;
        $short = substr($version, 0, strrpos($version, '.'));
        return "http://selenium-release.storage.googleapis.com/$short/{$this->getFileName()}";
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getOutputFileName()
    {
        $version = Versions::IEDRIVER;
        return "IEDriver_$version.zip";
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isSupported()
    {
        $system = $this->resolver->getSystem();
        return $system->isWindows();
    }

    /**
     * Remove old versions of the binary.
     *
     * @param $directory
     * @return void
     */
    protected function removeOldVersions($directory)
    {
        $paths = glob("$directory/IEDriver*");
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
        return $directory . '/' . str_replace(Versions::IEDRIVER, '*', $this->getOutputFileName());
    }

    /**
     * {@inheritdoc}
     *
     * @param string $directory
     * @return string
     */
    public function getDriverPath($directory)
    {
        $file = "$directory/{$this->getExtractedName()}";
        return "webdriver.ie.driver=$file";
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getExtractedName()
    {
        return 'IEDriverServer.exe';
    }
}
