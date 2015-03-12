<?php
namespace Peridot\WebDriverManager\Binary\Request;

use Peridot\WebDriverManager\Event\EventEmitterTrait;

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
                'method' => 'GET',
                'user_agent' => 'Peridot WebDriver Manager'
            ]
        ];
        $context = stream_context_create($context_options, [
            'notification' => [$this, 'onNotification']
        ]);
        $contents = file_get_contents($url, false, $context);
        $this->emit('complete', [$url]);
        return $contents;
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
     * @return void
     */
    public function onNotification($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max)
    {
        switch($notification_code) {
            case STREAM_NOTIFY_PROGRESS:
                $this->emit('progress', [$bytes_transferred]);
                break;
            case STREAM_NOTIFY_FILE_SIZE_IS:
                $this->emit('request.start', [$bytes_max]);
                break;
        }
    }
}
