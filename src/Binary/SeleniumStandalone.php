<?php
namespace Peridot\WebDriverManager\Binary;

use Peridot\WebDriverManager\Versions;

class SeleniumStandalone extends AbstractBinary
{
    /**
     * @param BinaryResolverInterface $resolver
     */
    public function __construct(BinaryResolverInterface $resolver)
    {
        parent::__construct($resolver);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'selenium';
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

    /**
     * {@inheritdoc}
     *
     * @param string $directory
     * @return string
     */
    protected function getOldFilePattern($directory)
    {
        return $directory . '/' . str_replace(Versions::SELENIUM, '*', $this->getFileName());
    }
}
