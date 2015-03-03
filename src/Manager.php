<?php
namespace Peridot\WebDriverManager;

use Lurker\Exception\RuntimeException;
use Peridot\WebDriverManager\Binary\Decompression\BinaryDecompressorInterface;
use Peridot\WebDriverManager\Binary\Request\BinaryRequestInterface;
use Peridot\WebDriverManager\Binary\ChromeDriver;
use Peridot\WebDriverManager\Binary\SeleniumStandalone;
use Peridot\WebDriverManager\Binary\Request\StandardBinaryRequest;
use Peridot\WebDriverManager\Binary\Decompression\ZipDecompressor;
use Peridot\WebDriverManager\OS\System;
use Peridot\WebDriverManager\OS\SystemInterface;

class Manager
{
    /**
     * @var array
     */
    protected $binaries;

    /**
     * @var BinaryRequestInterface
     */
    protected $request;

    /**
     * @var BinaryDecompressorInterface
     */
    protected $decompressor;

    /**
     * @var SystemInterface
     */
    protected $system;

    /**
     * @param BinaryRequestInterface $request
     * @param BinaryDecompressorInterface $decompressor
     */
    public function __construct(
        BinaryRequestInterface $request = null,
        BinaryDecompressorInterface $decompressor = null,
        SystemInterface $system = null
    ) {
        $this->request = $request;
        $this->decompressor = $decompressor;
        $this->system = $system;
        $this->binaries = [
            new SeleniumStandalone($this->getBinaryRequest()),
            new ChromeDriver($this->getBinaryRequest(), $this->getBinaryDecompressor(), $this->getSystem())
        ];
    }

    /**
     * Get the BinaryRequestInterface responsible for fetching remote binaries.
     *
     * @return BinaryRequestInterface|StandardBinaryRequest
     */
    public function getBinaryRequest()
    {
        if ($this->request === null) {
            return new StandardBinaryRequest();
        }
        return $this->request;
    }

    /**
     * Get the BinaryDecompressorInterface responsible for decompressing
     * compressed binaries.
     *
     * @return BinaryDecompressorInterface|ZipDecompressor
     */
    public function getBinaryDecompressor()
    {
        if ($this->decompressor === null) {
            return new ZipDecompressor();
        }
        return $this->decompressor;
    }

    /**
     * Get the System object responsible for determining operating system
     * information.
     *
     * @return System|SystemInterface
     */
    public function getSystem()
    {
        if ($this->system === null) {
            return new System();
        }

        return $this->system;
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
        $binary = array_reduce($this->binaries, function ($r, $i) use ($binaryName) {
            return $i->getName() === $binaryName ? $i : $r;
        });

        if (! $binary) {
            throw new RuntimeException("Binary named $binaryName does not exist");
        }

        $binary->fetchAndSave($this->getInstallPath());
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
