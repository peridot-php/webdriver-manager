<?php
namespace Peridot\WebDriverManager;

use Peridot\WebDriverManager\Binary\ChromeDriver;
use Peridot\WebDriverManager\Binary\SeleniumStandalone;

class Manager
{
    /**
     * @var array
     */
    protected $binaries;

    public function __construct()
    {
        $this->binaries = [
            new SeleniumStandalone(),
            new ChromeDriver()
        ];
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
