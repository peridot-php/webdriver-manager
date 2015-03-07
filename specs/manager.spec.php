<?php
use Peridot\WebDriverManager\Binary\BinaryResolver;
use Peridot\WebDriverManager\Manager;
use Peridot\WebDriverManager\Test\TestDecompressor;
use Prophecy\Argument;

describe('Manager', function () {
    beforeEach(function () {
        $this->system = $this->getProphet()->prophesize('Peridot\WebDriverManager\OS\SystemInterface');
        $this->request = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\Request\BinaryRequestInterface');
        $this->decompressor = new TestDecompressor();

        $this->resolver = new BinaryResolver($this->request->reveal(), $this->decompressor, $this->system->reveal());
        $this->request->request(Argument::any())->willReturn('string');

        $this->manager = new Manager($this->resolver);
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
});
