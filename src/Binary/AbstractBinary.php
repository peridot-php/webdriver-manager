<?php
namespace Peridot\WebDriverManager\Binary;

/**
 * AbstractBinary is a base class for all binaries and drivers.
 *
 * @package Peridot\WebDriverManager\Binary
 */
abstract class AbstractBinary implements BinaryInterface
{
    /**
     * @var string
     */
    protected $contents;

    /**
     * @var BinaryResolverInterface
     */
    protected $resolver;

    /**
     * @param BinaryResolverInterface $resolver
     */
    public function __construct(BinaryResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function fetch()
    {
        $this->contents = $this->resolver->request($this->getUrl());
        return $this->contents !== false;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $directory
     * @return bool
     */
    public function save($directory)
    {
        if ($this->exists($directory)) {
            return true;
        }

        if (! $this->contents) {
            return false;
        }

        $output = $this->getDestination($directory);
        $this->removeOldVersions($directory);
        return file_put_contents($output, $this->contents) !== false;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $directory
     * @return bool
     */
    public function fetchAndSave($directory)
    {
        if ($this->exists($directory)) {
            return true;
        }

        return $this->fetch() && $this->save($directory);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * {@inheritdoc}
     *
     * @param $directory
     * @return bool
     */
    public function exists($directory)
    {
        return file_exists($this->getDestination($directory));
    }

    /**
     * {@inheritdoc}
     *
     * @param $directory
     * @return bool
     */
    public function isOutOfDate($directory)
    {
        if ($this->exists($directory)) {
            return false;
        }

        $pattern = $this->getOldFilePattern($directory);
        $matches = glob($pattern);

        return count($matches) > 0;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isSupported()
    {
        return true;
    }

    /**
     * Get the destination file for the binary.
     *
     * @param $directory
     * @return string
     */
    protected function getDestination($directory)
    {
        return "$directory/{$this->getFileName()}";
    }

    /**
     * Remove old versions of the binary.
     *
     * @param $directory
     * @return void
     */
    protected function removeOldVersions($directory)
    {

    }

    /**
     * Return a pattern to identify old versions of a binary.
     *
     * @param string $directory
     * @return string
     */
    abstract protected function getOldFilePattern($directory);
}
