<?php
namespace Peridot\WebDriverManager\Binary\Decompression;

interface BinaryDecompressorInterface
{
    /**
     * Extract a compressed file to the given directory.
     *
     * @param string $compressedFilePath
     * @param string $directory
     * @return bool
     */
    public function extract($compressedFilePath, $directory);
} 
