<?php
namespace Peridot\WebDriverManager\Test;

use Peridot\WebDriverManager\Binary\Decompression\BinaryDecompressorInterface;

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
