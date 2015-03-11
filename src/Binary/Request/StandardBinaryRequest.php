<?php
namespace Peridot\WebDriverManager\Binary\Request;

use Evenement\EventEmitterTrait;

/**
 * StandardBinaryRequest uses file_get_contents with a stream context.
 * @package Peridot\WebDriverManager\Binary
 */
class StandardBinaryRequest implements BinaryRequestInterface
{
    use EventEmitterTrait;

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
        stream_context_set_params($context, ['notification' => [$this, 'onNotification']]);
        return file_get_contents($url, null, $context);
    }

    /**
     * Callback used when progress is made requesting.
     *
     * @param $notification_code
     * @param $severity
     * @param $message
     * @param $message_code
     * @param $bytes_transferred
     * @param $bytes_max
     */
    public function onNotification($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max)
    {
        if ($notification_code === STREAM_NOTIFY_FAILURE) {
            throw new \RuntimeException("Failure requesting binary");
        }
        
        $percent = 0;

        if ($bytes_max) {
            $percent = ($bytes_transferred / $bytes_max) * 100;
        }

        $this->emit('progress', [$percent]);
    }
}
