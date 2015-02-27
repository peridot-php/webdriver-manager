<?php
namespace Peridot\WebDriverManager\Binary\Request;

/**
 * StandardBinaryRequest uses file_get_contents with a stream context.
 * @package Peridot\WebDriverManager\Binary
 */
class StandardBinaryRequest implements BinaryRequestInterface
{
    /**
     * {@inheritdoc}
     *
     * @param $url
     * @return string
     */
    public function request($url)
    {
        $context_options = [
            'http' => [
                'method' => 'GET'
            ]
        ];
        $context = stream_context_create($context_options);
        return file_get_contents($url, null, $context);
    }
}
