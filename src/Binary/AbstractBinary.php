<?php
namespace Peridot\WebDriverManager\Binary;

use Peridot\WebDriverManager\Binary\Request\BinaryRequestInterface;
use Peridot\WebDriverManager\OS\SystemInterface;

abstract class AbstractBinary implements BinaryInterface
{
    /**
     * @var string
     */
    protected $contents;

    /**
     * @var BinaryRequestInterface
     */
    protected $request;

    /**
     * @var SystemInterface
     */
    protected $system;

    /**
     * @param BinaryRequestInterface $request
     */
    public function __construct(BinaryRequestInterface $request, SystemInterface $system)
    {
        $this->request = $request;
        $this->system = $system;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function fetch()
    {
        $this->contents = $this->request->request($this->getUrl());
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
        if (! $this->contents) {
            return false;
        }

        $this->removeOldVersions($directory);
        $output = "$directory/{$this->getFileName()}";
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
     * Remove old versions of the binary.
     *
     * @param $directory
     * @return void
     */
    protected function removeOldVersions($directory)
    {

    }
}
