<?php
use Peridot\WebDriverManager\Process\SeleniumProcess;

describe('SeleniumProcess', function () {
    beforeEach(function () {
        $this->process = new SeleniumProcess();
    });

    it('checks java availability', function () {
        expect($this->process->isAvailable())->to->be->true;
    });
});
