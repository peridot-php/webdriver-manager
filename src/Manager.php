<?php
namespace Peridot\WebDriverManager;

use Peridot\WebDriverManager\Binary\BinaryInterface;
use Peridot\WebDriverManager\Binary\BinaryResolver;
use Peridot\WebDriverManager\Binary\BinaryResolverInterface;
use Peridot\WebDriverManager\Binary\ChromeDriver;
use Peridot\WebDriverManager\Binary\DriverInterface;
use Peridot\WebDriverManager\Binary\IEDriver;
use Peridot\WebDriverManager\Binary\SeleniumStandalone;
use Peridot\WebDriverManager\Event\EventEmitterInterface;
use Peridot\WebDriverManager\Event\EventEmitterTrait;
use Peridot\WebDriverManager\Process\SeleniumProcessInterface;
use Peridot\WebDriverManager\Process\SeleniumProcess;
use RuntimeException;

/**
 * The Manager provides an api for controlling Selenium Server. It can be used
 * to keep binaries and drivers up to date, as well as start Selenium Server.
 *
 * @package Peridot\WebDriverManager
 */
class Manager implements EventEmitterInterface
{
    use EventEmitterTrait;

    /**
     * @var array
     */
    protected $binaries;

    /**
     * @var BinaryResolverInterface
     */
    protected $resolver;

    /**
     * @var SeleniumProcessInterface
     */
    protected $process;

    /**
     * @var string
     */
    protected $installPath = '';

    /**
     * @param BinaryResolverInterface $resolver
     * @param SeleniumProcessInterface $process
     */
    public function __construct(BinaryResolverInterface $resolver = null, SeleniumProcessInterface $process = null) {
        $this->resolver = $resolver;
        $this->process = $process;
        $this->binaries = [];

        $resolver = $this->getBinaryResolver();
        $this->addBinary(new SeleniumStandalone($resolver));
        $this->addBinary(new ChromeDriver($resolver));
        $this->addBinary(new IEDriver($resolver));
        $this->setBinaryResolver($resolver);
    }

    /**
     * Add a binary to the collection of managed binaries.
     *
     * @param BinaryInterface $binary
     */
    public function addBinary(BinaryInterface $binary)
    {
        $this->binaries[$binary->getName()] = $binary;
    }

    /**
     * Remove a binary from the collection of managed binaries.
     *
     * @param string $binaryName
     */
    public function removeBinary($binaryName)
    {
        if (isset($this->binaries[$binaryName])) {
            unset($this->binaries[$binaryName]);
        }
    }

    /**
     * Return the BinaryResolver used to resolve binary files.
     *
     * @return BinaryResolver|BinaryResolverInterface
     */
    public function getBinaryResolver()
    {
        if ($this->resolver === null) {
            $this->resolver = new BinaryResolver();
        }

        return $this->resolver;
    }

    /**
     * Set the BinaryResolver used to resolve binary files.
     *
     * @param BinaryResolverInterface $resolver
     */
    public function setBinaryResolver(BinaryResolverInterface $resolver)
    {
        $this->resolver = $resolver;
        $this->inherit(['progress', 'request.start', 'complete'], $resolver);
    }

    /**
     * Return the SeleniumProcessInterface that will execute the
     * selenium server command.
     *
     * @return SeleniumProcess|SeleniumProcessInterface
     */
    public function getSeleniumProcess()
    {
        if ($this->process === null) {
            $this->process = new SeleniumProcess();
        }

        return $this->process;
    }

    /**
     * Return all managed binaries.
     *
     * @param callable $predicate
     * @return array
     */
    public function getBinaries(callable $predicate = null)
    {
        $binaries = $this->binaries;
        if ($predicate !== null) {
            return array_filter($binaries, $predicate);
        }
        return $binaries;
    }

    /**
     * Return all binaries that are considered drivers.
     *
     * @return array
     */
    public function getDrivers()
    {
        return $this->getBinaries(function ($binary) {
            return $binary instanceof DriverInterface;
        });
    }

    /**
     * Pending binaries are binaries that are supported but have not been installed.
     *
     * @return array
     */
    public function getPendingBinaries()
    {
        return $this->getBinaries(function (BinaryInterface $binary) {
            $exists = $binary->exists($this->getInstallPath());
            $supported = $binary->isSupported();
            return $supported && !$exists;
        });
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
     * @param bool $background
     * @param int $port
     * @param array $args
     * @return SeleniumProcessInterface
     */
    public function start($background = false, $port = 4444, array $args = [])
    {
        $selenium = $this->binaries['selenium'];
        $this->assertStartConditions($selenium);

        $process = $this->getSeleniumProcess();
        $this->registerBinaries($process, $selenium);

        if ($port != 4444) {
            $process->addArg('-port', $port);
        }

        if (!empty($args)) {
            $process->addArgs($args);
        }

        return $process->start($background);
    }

    /**
     * Start Selenium in the foreground.
     *
     * @param int $port
     * @return SeleniumProcessInterface
     */
    public function startInForeground($port = 4444, array $args = [])
    {
        return $this->start(false, $port, $args);
    }

    /**
     * Start Selenium in a background process.
     *
     * @param int $port
     * @return SeleniumProcessInterface
     */
    public function startInBackground($port = 4444, array $args = [])
    {
        return $this->start(true, $port, $args);
    }

    /**
     * Remove all binaries from the install path.
     *
     * @return void
     */
    public function clean()
    {
        $files = glob($this->getInstallPath() . '/*');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    /**
     * Get the installation path of binaries.
     *
     * @return string
     */
    public function getInstallPath()
    {
        if ($this->installPath === '') {
            $this->installPath = realpath(__DIR__ . '/../binaries');
        }

        return $this->installPath;
    }

    /**
     * Set the installation path for binaries.
     *
     * @param string $path
     */
    public function setInstallPath($path)
    {
        $this->installPath = $path;
    }

    /**
     * Assert that the selenium server can start.
     *
     * @param SeleniumStandalone $selenium
     * @throws \RuntimeException
     * @return void
     */
    protected function assertStartConditions(SeleniumStandalone $selenium)
    {
        if (!$selenium->exists($this->getInstallPath())) {
            throw new RuntimeException("Selenium Standalone binary not installed");
        }

        if (!$this->getSeleniumProcess()->isAvailable()) {
            throw new RuntimeException('java is not available');
        }
    }

    /**
     * Register selenium binary and drivers with the process.
     *
     * @param SeleniumProcessInterface $process
     * @param SeleniumStandalone $selenium
     * @return void
     */
    protected function registerBinaries(SeleniumProcessInterface $process, SeleniumStandalone $selenium)
    {
        $process->addBinary($selenium, $this->getInstallPath());
        $drivers = $this->getDrivers();
        foreach ($drivers as $driver) {
            $process->addBinary($driver, $this->getInstallPath());
        }
    }
}
