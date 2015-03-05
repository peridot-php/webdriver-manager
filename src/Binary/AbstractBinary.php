<?php
namespace Peridot\WebDriverManager\Binary;

use Peridot\WebDriverManager\Binary\Request\BinaryRequestInterface;

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
     * @param BinaryRequestInterface $request
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
}
