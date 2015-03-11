<?php
use Peridot\WebDriverManager\Binary\Request\StandardBinaryRequest;

describe('StandardBinaryRequest', function () {
    beforeEach(function () {
        $this->request = new StandardBinaryRequest();
    });

    describe('->onNotification()', function () {
        it('should throw an exception if a failure is received', function () {
            expect(function () {
                $this->request->onNotification(STREAM_NOTIFY_FAILURE, 0, 'message', 2, 0, 0);
            })->to->throw('RuntimeException');
        });

        it('should return 0 if there are no max bytes', function () {
            $percent = -1;
            $this->request->on('progress', function ($p) use (&$percent) {
                $percent = $p;
            });
            $this->request->onNotification(STREAM_NOTIFY_FILE_SIZE_IS, 0, 'message', 2, 0, 0);
            expect($percent)->to->equal(0);
        });

        it('should return transferred divided by byte_max when both present', function () {
            $percent = -1;
            $this->request->on('progress', function ($p) use (&$percent) {
                $percent = $p;
            });
            $this->request->onNotification(STREAM_NOTIFY_PROGRESS, 0, 'message', 2, 25, 50);
            expect($percent)->to->loosely->equal(50);
        });
    });
});
