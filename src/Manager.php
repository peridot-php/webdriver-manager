<?php
namespace Peridot\WebDriverManager;

use Peridot\WebDriverManager\Binary\BinaryResolver;
use Peridot\WebDriverManager\Binary\BinaryResolverInterface;
use Peridot\WebDriverManager\Binary\ChromeDriver;
use Peridot\WebDriverManager\Binary\DriverInterface;
use Peridot\WebDriverManager\Binary\SeleniumStandalone;
use Peridot\WebDriverManager\Process\JavaProcessInterface;
use Peridot\WebDriverManager\Process\SeleniumProcess;
use RuntimeException;


class Manager
{
    /**
     * @var array
     */
    protected $binaries;

    /**
     * @var BinaryResolverInterface
     */
    protected $resolver;

    /**
     * @var JavaProcessInterface
     */
    protected $java;

    /**
     * @param BinaryResolverInterface $resolver
     */
    public function __construct(BinaryResolverInterface $resolver = null, JavaProcessInterface $java = null) {
        $this->resolver = $resolver;
        $this->java = $java;

        $selenium = new SeleniumStandalone($this->getBinaryResolver());
        $chrome = new ChromeDriver($this->getBinaryResolver());

        $this->binaries = [
            $selenium->getName() => $selenium,
            $chrome->getName() => $chrome
        ];
    }

    /**
     * Return the BinaryResolver used to resolve binary files.
     *
     * @return BinaryResolver|BinaryResolverInterface
     */
    public function getBinaryResolver()
    {
        if ($this->resolver === null) {
            return new BinaryResolver();
        }

        return $this->resolver;
    }

    public function getJavaProcess()
    {
        if ($this->java === null) {
            return new SeleniumProcess();
        }

        return $this->java;
    }

    /**
     * Return all managed binaries.
     *
     * @return array
     */
    public function getBinaries()
    {
        return $this->binaries;
    }

    /**
     * Return all binaries that are considered drivers.
     *
     * @return array
     */
    public function getDrivers()
    {
        $drivers = array_filter($this->binaries, function ($binary) {
            return $binary instanceof DriverInterface;
        });

        return array_values($drivers);
    }

    /**
     * Fetch and save binaries.
     *
     * @return bool
     */
    public function update($binaryName = '')
    {
        if ($binaryName) {
            $this->updateSingle($binaryName);
            return;
        }

        foreach ($this->binaries as $binary) {
            $binary->fetchAndSave($this->getInstallPath());
        }
    }

    /**
     * Update a single binary.
     *
     * @param $binaryName
     * @return void
     */
    public function updateSingle($binaryName)
    {
        if (! array_key_exists($binaryName, $this->binaries)) {
            throw new RuntimeException("Binary named $binaryName does not exist");
        }

        $binary = $this->binaries[$binaryName];
        $binary->fetchAndSave($this->getInstallPath());
    }

    /**
     * Start the Selenium server.
     *
     * @param int $port
     */
    public function start($port = -1)
    {
        $selenium = $this->binaries['selenium'];

        if (! $selenium->exists($this->getInstallPath())) {
            throw new RuntimeException("Selenium Standalone binary not installed");
        }

        if (! $this->java->isAvailable()) {
            throw new RuntimeException('java is not available');
        }

        $this->java->addBinary($selenium, $this->getInstallPath());

        if ($port != -1) {
            $this->java->addArg('-port', $port);
        }

        return $this->java->start();
    }

    /**
     * Get the installation path of binaries.
     *
     * @return string
     */
    public function getInstallPath()
    {
        return realpath(__DIR__ . '/../binaries');
    }
}
