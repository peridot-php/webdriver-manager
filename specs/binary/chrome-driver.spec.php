<?php
use Peridot\WebDriverManager\Binary\BinaryResolver;
use Peridot\WebDriverManager\Binary\ChromeDriver;
use Peridot\WebDriverManager\Versions;

describe('ChromeDriver', function () {
    describe('->isOutOfDate()', function () {
        beforeEach(function () {
            $this->binary = new ChromeDriver(new BinaryResolver());
            $this->current = __DIR__ . '/' . $this->binary->getOutputFileName();
            $this->old = str_replace(Versions::CHROMEDRIVER, '0.1.1', $this->current);
        });

        require 'shared/is-out-of-date.php';
    });

    describe('->getLinuxFileName()', function () {
       it('should return a 64 bit linux name', function () {
           $os = $this->getProphet()->prophesize('Peridot\WebDriverManager\OS\SystemInterface');
           $os->is64Bit()->willReturn(true);
           $binary = new ChromeDriver(new BinaryResolver(null, null, $os->reveal()));
           expect($binary->getLinuxFileName())->to->equal('linux64');
       });
    });

    describe('->getExtractedName()', function () {
        it('should return the name of the executable', function () {
            expect($this->binary->getExtractedName())->to->equal('chromedriver');
        });
    });
});
