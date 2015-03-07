<?php
namespace Peridot\WebDriverManager;

use Peridot\WebDriverManager\Binary\BinaryResolver;
use Peridot\WebDriverManager\Binary\BinaryResolverInterface;
use Peridot\WebDriverManager\Binary\ChromeDriver;
use Peridot\WebDriverManager\Binary\SeleniumStandalone;
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

    public function __construct(BinaryResolverInterface $resolver = null) {
        $this->resolver = $resolver;

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
            $binary->fetchAndSave($this->resolver->getInstallPath());
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
        if (!array_key_exists($binaryName, $this->binaries)) {
            throw new RuntimeException("Binary named $binaryName does not exist");
        }

        $binary = $this->binaries[$binaryName];
        $binary->fetchAndSave($this->resolver->getInstallPath());
    }
}
