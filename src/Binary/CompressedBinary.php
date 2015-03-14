<?php
namespace Peridot\WebDriverManager\Binary;

/**
 * CompressedBinary extends the AbstractBinary to include behavior
 * for handling compressed binaries.
 *
 * @package Peridot\WebDriverManager\Binary
 */
abstract class CompressedBinary extends AbstractBinary
{
    /**
     * Overrides default save behavior to first decompress
     * an archive format before saving it to a directory.
     *
     * @param string $directory
     * @return bool
     */
    public function save($directory)
    {
        if (empty($this->contents)) {
            return false;
        }

        if ($this->exists($directory)) {
            return true;
        }

        $compressedPath = $directory . DIRECTORY_SEPARATOR . $this->getOutputFileName();
        $this->removeOldVersions($directory);
        file_put_contents($compressedPath, $this->contents);
        $extracted = $this->resolver->extract($compressedPath, $directory);

        if ($extracted) {
            chmod("$directory/{$this->getExtractedName()}", 0777);
        }

        return $extracted;
    }

    /**
     * {@inheritdoc}
     *
     * @param $directory
     * @return bool
     */
    public function exists($directory)
    {
        return file_exists("$directory/{$this->getOutputFileName()}");
    }

    /**
     * Return the output filename for the compressed binary.
     *
     * @return string
     */
    abstract public function getOutputFileName();

    /**
     * Get the name of the extracted binary.
     *
     * @return string
     */
    abstract public function getExtractedName();
}
