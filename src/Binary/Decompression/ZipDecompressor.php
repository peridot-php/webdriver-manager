<?php
namespace Peridot\WebDriverManager\Binary\Decompression;

use splitbrain\PHPArchive\Zip;

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
