<?php
namespace Peridot\WebDriverManager\Binary\Request;

use Peridot\WebDriverManager\Event\EventEmitterTrait;

/**
 * StandardBinaryRequest uses file_get_contents with a stream context. It is capable of emitting
 * download progress events containing bytes transferred, and bytes total.
 *
 * @package Peridot\WebDriverManager\Binary
 */
class StandardBinaryRequest implements BinaryRequestInterface
{
    use EventEmitterTrait;

    /**
     * @var string
     */
    private $url;

    /**
     * {@inheritdoc}
     *
     * @param $url
     * @return string
     */
    public function request($url)
    {
        $this->url = $url;
        $context_options = [
            'http' => [
                'method' => 'GET',
                'user_agent' => 'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0'
            ]
        ];
        $context = stream_context_create($context_options, [
            'notification' => [$this, 'onNotification']
        ]);
        $contents = file_get_contents($url, false, $context);
        $this->emit('complete');
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
                $this->emit('request.start', [$this->url, $bytes_max]);
                break;
        }
    }
}
