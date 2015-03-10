<?php
use Peridot\WebDriverManager\Binary\BinaryResolver;
use Peridot\WebDriverManager\Binary\ChromeDriver;
use Peridot\WebDriverManager\Binary\SeleniumStandalone;
use Peridot\WebDriverManager\Process\SeleniumProcess;

describe('SeleniumProcess', function () {
    beforeEach(function () {
        $this->process = new SeleniumProcess();
        $this->fixtures = __DIR__ . '/../fixtures';
        $current = glob($this->fixtures . '/*');

        foreach ($current as $file) {
            unlink($file);
        }
    });

    it('should initialize with a -jar argument', function () {
        $args = $this->process->getArgs();
        expect($args)->to->contain('-jar');
    });

    describe('adding a driver', function () {
        beforeEach(function () {
            $this->os = $this->getProphet()->prophesize('Peridot\WebDriverManager\OS\SystemInterface');
            $this->driver = new ChromeDriver(new BinaryResolver(null, null, $this->os->reveal()));
            $this->selenium = new SeleniumStandalone(new BinaryResolver());
            file_put_contents($this->fixtures . '/' . $this->driver->getOutputFileName(), 'zipzipzip');
            file_put_contents($this->fixtures . '/' . $this->selenium->getFileName(), 'seleniumiscooool');
        });

        beforeEach(function () {
            $this->os->isWindows()->willReturn(false);
            $this->os->isLinux()->willReturn(false);
            $this->os->isMac()->willReturn(true);
        });

        it('should add the driver to the argument list', function () {
            $this->process->addDriver($this->driver, $this->fixtures);
            $args = $this->process->getArgs();
            expect($args)->to->contain("-D{$this->driver->getDriverPath($this->fixtures)}");
        });

        it('should not add the driver if the target does not exist', function () {
            unlink($this->fixtures . '/' . $this->driver->getOutputFileName());
            $this->process->addDriver($this->driver, $this->fixtures);
            $args = $this->process->getArgs();
            expect($args)->to->not->contain("-D{$this->driver->getDriverPath($this->fixtures)}");
        });

        it('should not add any arguments if the binary is not a driver', function () {
            $this->process->addDriver($this->selenium, $this->fixtures);
            $args = $this->process->getArgs();
            expect($args)->to->have->length(1, 'should only have default -jar argument');
        });

        context('when on windows', function () {
            beforeEach(function () {
                $this->os->isWindows()->willReturn(true);
                $this->os->isLinux()->willReturn(false);
                $this->os->isMac()->willReturn(false);
            });

            it('should add an .exe', function () {
                $this->process->addDriver($this->driver, $this->fixtures);
                $args = $this->process->getArgs();
                expect($args[1])->to->match('/[.]exe$/');
            });
        });
    });
});
