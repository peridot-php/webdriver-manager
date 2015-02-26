<?php
namespace Peridot\WebDriverManager;

use Peridot\WebDriverManager\Binary\BinaryRequestInterface;
use Peridot\WebDriverManager\Binary\ChromeDriver;
use Peridot\WebDriverManager\Binary\SeleniumStandalone;
use Peridot\WebDriverManager\Binary\StandardBinaryRequest;

class Manager
{
    /**
     * @var array
     */
    protected $binaries;

    /**
     * @param BinaryRequestInterface $request
     */
    public function __construct(BinaryRequestInterface $request = null)
    {
        $this->request = $request;
        $this->binaries = [
            new SeleniumStandalone($this->getBinaryRequest()),
            new ChromeDriver($this->getBinaryRequest())
        ];
    }

    /**
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
     * Fetch and save binaries.
     *
     * @return void
     */
    public function update()
    {
        foreach ($this->binaries as $binary) {
            $binary->fetchAndSave($this->getInstallPath());
        }
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
