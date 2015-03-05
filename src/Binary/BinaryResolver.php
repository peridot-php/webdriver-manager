<?php
namespace Peridot\WebDriverManager\Binary;

use Peridot\WebDriverManager\Binary\Decompression\BinaryDecompressorInterface;
use Peridot\WebDriverManager\Binary\Decompression\ZipDecompressor;
use Peridot\WebDriverManager\Binary\Request\BinaryRequestInterface;
use Peridot\WebDriverManager\Binary\Request\StandardBinaryRequest;
use Peridot\WebDriverManager\OS\System;
use Peridot\WebDriverManager\OS\SystemInterface;

class BinaryResolver implements BinaryRequestInterface, BinaryDecompressorInterface, BinaryResolverInterface
{
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
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     *
     * @param string $compressedFilePath
     * @param string $directory
     * @return bool
     */
    public function extract($compressedFilePath, $directory)
    {
        return $this->getBinaryDecompressor()->extract($compressedFilePath, $directory);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $url
     * @return string
     */
    public function request($url)
    {
        return $this->getBinaryRequest()->request($url);
    }
}
