<?php
namespace Peridot\WebDriverManager\Binary;

use Peridot\WebDriverManager\Binary\Decompression\BinaryDecompressorInterface;
use Peridot\WebDriverManager\Binary\Request\BinaryRequestInterface;
use Peridot\WebDriverManager\OS\SystemInterface;

abstract class CompressedBinary extends AbstractBinary
{
    protected $decompressor;

    /**
     * @param BinaryRequestInterface $request
     * @param BinaryDecompressorInterface $decompressor
     */
    public function __construct(BinaryRequestInterface $request, BinaryDecompressorInterface $decompressor, SystemInterface $system)
    {
        parent::__construct($request, $system);
        $this->decompressor = $decompressor;
    }

    /**
     * Overrides default save behavior to first decompress
     * an archive format before saving it to a directory.
     *
     * @param string $directory
     * @return bool
     */
    public function save($directory)
    {
        $this->removeOldVersions($directory);
        $compressedPath = $directory . DIRECTORY_SEPARATOR . $this->getOutputFileName();
        file_put_contents($compressedPath, $this->contents);
        return $this->decompressor->extract($compressedPath, $directory);
    }

    /**
     * Return the output filename for the compressed binary.
     *
     * @return string
     */
    abstract public function getOutputFileName();
}
