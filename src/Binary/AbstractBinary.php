<?php
namespace Peridot\WebDriverManager\Binary;

abstract class AbstractBinary implements BinaryInterface
{
    /**
     * @var string
     */
    protected $contents;

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function fetch()
    {
        $context_options = [
            'http' => [
                'method' => 'GET'
            ]
        ];
        $context = stream_context_create($context_options);
        $this->contents = file_get_contents($this->getUrl(), null, $context);
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
}
