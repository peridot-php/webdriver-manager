<?php
use Peridot\WebDriverManager\Binary\Request\StandardBinaryRequest;

describe('StandardBinaryRequest', function () {
    beforeEach(function () {
        $this->request = new StandardBinaryRequest();
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

        it('should emit a start event with total bytes', function () {
            $bytes = -1;
            $this->request->on('request.start', function ($b) use (&$bytes) {
                $bytes = $b;
            });
            $this->request->onNotification(STREAM_NOTIFY_FILE_SIZE_IS, 0, 'message', 0, 0, 50);
            expect($bytes)->to->equal(50);
        });
    });
});
