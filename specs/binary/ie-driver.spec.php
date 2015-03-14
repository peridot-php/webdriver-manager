<?php
use Peridot\WebDriverManager\Binary\BinaryResolver;
use Peridot\WebDriverManager\Binary\IEDriver;
use Peridot\WebDriverManager\Versions;

describe('IEDriver', function () {
    describe('->isOutOfDate()', function () {
        beforeEach(function () {
            $system = $this->getProphet()->prophesize('Peridot\WebDriverManager\OS\SystemInterface');

            $system->isMac()->willReturn(false);
            $system->isWindows()->willReturn(true);
            $system->isLinux()->willReturn(false);

            $this->binary = new IEDriver(new BinaryResolver(null, null, $system->reveal()));
            $this->current = __DIR__ . '/' . $this->binary->getOutputFileName();
            $this->old = str_replace(Versions::IEDRIVER, '0.1.1', $this->current);
        });

        require 'shared/is-out-of-date.php';
    });

    describe('->getExtractedName()', function () {
        it('should return the name of the executable', function () {
            expect($this->binary->getExtractedName())->to->equal('IEDriverServer.exe');
        });
    });
});
