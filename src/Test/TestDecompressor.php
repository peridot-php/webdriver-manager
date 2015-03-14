<?php
namespace Peridot\WebDriverManager\Test;

use Peridot\WebDriverManager\Binary\Decompression\BinaryDecompressorInterface;

/**
 * TestDecompressor is used as a stub for testing decompression.
 *
 * @package Peridot\WebDriverManager\Test
 */
class TestDecompressor implements BinaryDecompressorInterface
{
    public $decompressedPath;

    public $targetPath;

    public function setTargetPath($path)
    {
        $this->targetPath = $path;
    }

    public function extract($compressedFilePath, $directory)
    {
        $this->decompressedPath = $compressedFilePath;
        file_put_contents($this->targetPath, 'binarydata');
    }
} 
