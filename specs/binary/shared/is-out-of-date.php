<?php
beforeEach(function () {
    foreach([$this->current, $this->old] as $path) {
        if (file_exists($path)) {
            unlink($path);
        }
    }
    file_put_contents($this->current, 'data is coooool');
    copy($this->current, $this->old);
});

it('should return false if an up to date version is present', function () {
    expect($this->binary->isOutOfDate(dirname(__DIR__)))->to->be->false;
});

it('should return true if there is no current version and there is an old version', function () {
    unlink($this->current);
    expect($this->binary->isOutOfDate(dirname(__DIR__)))->to->be->true;
});

it('should return false if there are no current or old versions', function () {
    unlink($this->current);
    unlink($this->old);
    expect($this->binary->isOutOfDate(dirname(__DIR__)))->to->be->false;
});
