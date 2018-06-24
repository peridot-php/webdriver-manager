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

    it('should not initialize with a -jar argument', function () {
        $args = $this->process->getArgs();
        expect($args)->to->not->contain('-jar');
    });

    describe('->addArg', function () {
        it('should support variadic arguments', function () {
            $this->process->addArg('-port', 9000);
            $args = $this->process->getArgs();
            expect($args)->to->contain('-port')->and->to->contain(9000);
        });
    });

    describe('->getCommand()', function () {
        it('should return a java command with the arguments joined', function () {
            $this->process->addArg('-port', 9000);
            $command = $this->process->getCommand();
            $prefix = strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' ? 'exec ' : '';
            expect($command)->to->equal($prefix . 'java -port 9000');
        });
    });

    describe('adding a binary', function () {
        beforeEach(function () {
            $this->os = $this->getProphet()->prophesize('Peridot\WebDriverManager\OS\SystemInterface');
            $this->driver = new ChromeDriver(new BinaryResolver(null, null, $this->os->reveal()));
            $this->selenium = new SeleniumStandalone(new BinaryResolver());
            file_put_contents($this->fixtures . '/' . $this->driver->getOutputFileName(), 'zipzipzip');
            file_put_contents($this->fixtures . '/' . $this->selenium->getFileName(), 'seleniumiscooool.jar');
        });

        beforeEach(function () {
            $this->os->isWindows()->willReturn(false);
            $this->os->isLinux()->willReturn(false);
            $this->os->isMac()->willReturn(true);
        });

        it('should add the driver to the argument list', function () {
            $this->process->addBinary($this->driver, $this->fixtures);
            $args = $this->process->getArgs();
            expect($args)->to->contain("-D{$this->driver->getDriverPath($this->fixtures)}");
        });

        it('should not add the driver if the target does not exist', function () {
            unlink($this->fixtures . '/' . $this->driver->getOutputFileName());
            $this->process->addBinary($this->driver, $this->fixtures);
            $args = $this->process->getArgs();
            expect($args)->to->not->contain("-D{$this->driver->getDriverPath($this->fixtures)}");
        });

        it('should add the binary path if not a driver', function () {
            $this->process->addBinary($this->selenium, $this->fixtures);
            $args = $this->process->getArgs();
            expect($args)->to->contain(realpath($this->fixtures . '/' . $this->selenium->getFileName()));

        });

        it('should add the -jar if is java program', function () {
            $this->process->addBinary($this->selenium, $this->fixtures);
            $args = $this->process->getArgs();
            expect($args)->to->contain('-jar');
            expect($args)->to->contain(realpath($this->fixtures . '/' . $this->selenium->getFileName()));

        });

        context('when on windows', function () {
            beforeEach(function () {
                $this->os->isWindows()->willReturn(true);
                $this->os->isLinux()->willReturn(false);
                $this->os->isMac()->willReturn(false);
            });

            it('should add an .exe', function () {
                $this->process->addBinary($this->driver, $this->fixtures);
                $args = $this->process->getArgs();
                expect($args[0])->to->match('/[.]exe$/');
            });
        });
    });
});
