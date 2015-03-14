<?php
namespace Peridot\WebDriverManager\Binary\Decompression;

use splitbrain\PHPArchive\Zip;

/**
 * ZipDecompressor extracts binaries contained in zip files.
 *
 * @package Peridot\WebDriverManager\Binary\Decompression
 */
class ZipDecompressor implements BinaryDecompressorInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string $compressedFilePath
     * @param string $directory
     * @return bool
     */
    public function extract($compressedFilePath, $directory)
    {
        $zip = new Zip();
        $zip->open($compressedFilePath);
        $info = $zip->extract($directory);
        return !empty($info);
    }
}
