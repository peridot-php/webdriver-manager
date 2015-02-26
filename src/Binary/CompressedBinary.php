<?php
namespace Peridot\WebDriverManager\Binary;

abstract class CompressedBinary extends AbstractBinary
{
    protected $decompressor;

    /**
     * @param BinaryRequestInterface $request
     * @param BinaryDecompressorInterface $decompressor
     */
    public function __construct(BinaryRequestInterface $request, BinaryDecompressorInterface $decompressor)
    {
        parent::__construct($request);
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
        $compressedPath = tempnam(sys_get_temp_dir(), "WDM_");
        file_put_contents($compressedPath, $this->contents);
        return $this->decompressor->extract($compressedPath, $directory);
    }
}
