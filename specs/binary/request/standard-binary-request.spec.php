<?php
use Peridot\WebDriverManager\Binary\Request\StandardBinaryRequest;

describe('StandardBinaryRequest', function () {
    beforeEach(function () {
        $this->request = new StandardBinaryRequest();

        // url is a private property on the standard request that is set by ->request()
        // set up this way to simplify the event api
        $reflection = new ReflectionClass($this->request);
        $url = $reflection->getProperty('url');
        $url->setAccessible(true);
        $url->setValue($this->request, 'http://a.b');
    });

    describe('->onNotification()', function () {
        it('should emit progress event with bytes', function () {
            $bytes = -1;
            $this->request->on('progress', function ($p) use (&$bytes) {
                $bytes = $p;
            });
            $this->request->onNotification(STREAM_NOTIFY_PROGRESS, 0, 'message', 0, 25, 0);
            expect($bytes)->to->equal(25);
        });

        it('should emit a start event with the url and total bytes', function () {
            $url = '';
            $bytes = -1;
            $this->request->on('request.start', function ($u, $b) use (&$url, &$bytes) {
                $url = $u;
                $bytes = $b;
            });
            $this->request->onNotification(STREAM_NOTIFY_FILE_SIZE_IS, 0, 'message', 0, 0, 50);
            expect($bytes)->to->equal(50);
            expect($url)->to->equal('http://a.b');
        });
    });
});
