<?php
use Peridot\WebDriverManager\Binary\BinaryResolver;
use Peridot\WebDriverManager\Manager;
use Peridot\WebDriverManager\Process\SeleniumProcess;
use Peridot\WebDriverManager\Test\TestDecompressor;
use Peridot\WebDriverManager\Versions;
use Prophecy\Argument;

describe('Manager', function () {
    beforeEach(function () {
        $this->system = $this->getProphet()->prophesize('Peridot\WebDriverManager\OS\SystemInterface');
        $this->request = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\Request\BinaryRequestInterface');
        $this->decompressor = new TestDecompressor();

        $this->resolver = new BinaryResolver($this->request->reveal(), $this->decompressor, $this->system->reveal());
        $this->request->request(Argument::any())->willReturn('string');
        $this->java = $this->getProphet()->prophesize('Peridot\WebDriverManager\Process\JavaProcessInterface');

        $this->manager = new Manager($this->resolver, $this->java->reveal());
        $this->decompressor->setTargetPath($this->manager->getInstallPath() . '/chromedriver');
    });

    beforeEach(function () {
        $path = $this->manager->getInstallPath();
        $selenium = glob("$path/selenium-server-standalone-*");
        $chrome = glob("$path/chromedriver*");
        $files = array_merge($selenium, $chrome);
        foreach ($files as $file) {
            unlink($file);
        }
    });

    describe('->getInstallPath()', function () {
        it('should return a default path', function () {
            $path = realpath(__DIR__ . '/../binaries');
            expect($this->manager->getInstallPath())->to->equal($path);
        });
    });

    describe('->getBinaryResolver()', function () {
        it('should return a BinaryResolver by default', function () {
            $manager = new Manager();
            expect($manager->getBinaryResolver())->to->be->an->instanceof('Peridot\WebDriverManager\Binary\BinaryResolver');
        });

        it('should return the BinaryResolverInterface if given', function () {
            $resolver = new BinaryResolver();
            $manager = new Manager($resolver);
            expect($manager->getBinaryResolver())->to->equal($resolver);
        });
    });

    describe('->getJavaProcess()', function () {
        it('should return a SeleniumProcess by default', function () {
            $manager = new Manager();
            expect($manager->getJavaProcess())->to->be->an->instanceof('Peridot\WebDriverManager\Process\SeleniumProcess');
        });

        it('should return the JavaProcessInterface if given', function () {
            $process = new SeleniumProcess();
            $manager = new Manager(null, $process);
            expect($manager->getJavaProcess())->to->equal($process);
        });
    });

    describe('->getBinaries()', function () {
        it('should return a collection of managed binaries', function () {
            $binaries = $this->manager->getBinaries();
            expect($binaries)->to->have->length(2);
            foreach ($binaries as $binary) {
                expect($binary)->to->be->an->instanceof('Peridot\WebDriverManager\Binary\BinaryInterface');
            }
        });
    });

    describe('->getDrivers()', function () {
        it('should return a collection of binaries that qualify as drivers', function () {
            $drivers = $this->manager->getDrivers();
            expect($drivers)->to->have->length(1);
            foreach ($drivers as $driver) {
                expect($driver)->to->be->an->instanceof('Peridot\WebDriverManager\Binary\DriverInterface');
            }
        });
    });

    describe('->update()', function () {
        afterEach(function () {
            $this->getProphet()->checkPredictions();
        });

        context('when on a mac operating system', function () {
            beforeEach(function () {
                $this->system->isMac()->willReturn(true);
                $this->system->isWindows()->willReturn(false);
                $this->system->isLinux()->willReturn(false);
            });

            require 'shared/manager-update.php';
        });

        context('when on a windows operating system', function () {
            beforeEach(function () {
                $this->system->isMac()->willReturn(false);
                $this->system->isWindows()->willReturn(true);
                $this->system->isLinux()->willReturn(false);
            });

            require 'shared/manager-update.php';
        });

        context('when on a linux operating system', function () {
            beforeEach(function () {
                $this->system->isMac()->willReturn(false);
                $this->system->isWindows()->willReturn(false);
                $this->system->isLinux()->willReturn(true);
                $this->system->is64Bit()->shouldBeCalled();
            });

            require 'shared/manager-update.php';
        });
    });

    describe('->start()', function () {
        it('should throw an exception if there is not selenium binary', function () {
            expect([$this->manager, 'start'])->to->throw('RuntimeException');
        });

        it('should throw an exception if java is not available on the system', function () {
            $version = Versions::SELENIUM;
            file_put_contents($this->manager->getInstallPath() . "/selenium-server-standalone-$version.jar", 'data');
            $this->java->isAvailable()->willReturn(false);
            expect([$this->manager, 'start'])->to->throw('RuntimeException', 'java is not available');
        });
    });
});
