<?php
use Peridot\WebDriverManager\Binary\Decompression\BinaryDecompressorInterface;
use Peridot\WebDriverManager\Binary\Decompression\ZipDecompressor;
use Peridot\WebDriverManager\Binary\Request\StandardBinaryRequest;
use Peridot\WebDriverManager\Manager;
use Peridot\WebDriverManager\OS\System;
use Prophecy\Argument;

describe('Manager', function () {
    beforeEach(function () {
        $this->request = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\Request\BinaryRequestInterface');
        $this->decompressor = new TestDecompressor();
        $this->system = $this->getProphet()->prophesize('Peridot\WebDriverManager\OS\SystemInterface');

        $this->manager = new Manager(
            $this->request->reveal(),
            $this->decompressor,
            $this->system->reveal()
        );

        $this->request->request(Argument::any())->willReturn('string');
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

    describe('->getBinaryRequest()', function () {
        it('should return a StandardBinaryRequest by default', function () {
            $manager = new Manager();
            expect($manager->getBinaryRequest())->to->be->an->instanceof('Peridot\WebDriverManager\Binary\Request\StandardBinaryRequest');
        });

        it('should return the BinaryRequestInterface if given', function () {
            $request = new StandardBinaryRequest();
            $manager = new Manager($request);
            expect($manager->getBinaryRequest())->to->equal($request);
        });
    });

    describe('->getBinaryDecompressor()', function () {
        it('should return a ZipDecompressor by default', function () {
            $manager = new Manager();
            expect($manager->getBinaryDecompressor())->to->be->an->instanceof('Peridot\WebDriverManager\Binary\Decompression\ZipDecompressor');
        });

        it('should return the BinaryDecompressorInterface if given', function () {
            $decompressor = new ZipDecompressor();
            $manager = new Manager(null, $decompressor);
            expect($manager->getBinaryDecompressor())->to->equal($decompressor);
        });
    });

    describe('->getSystem()', function () {
        it('should return a System by default', function () {
            $manager = new Manager();
            expect($manager->getSystem())->to->be->an->instanceof('Peridot\WebDriverManager\OS\System');
        });

        it('should return the SystemInterface if given', function () {
            $system = new System();
            $manager = new Manager(null, null, $system);
            expect($manager->getSystem())->to->equal($system);
        });
    });

    describe('->getInstallPath()', function () {
        it('should return a default path', function () {
            $path = realpath(__DIR__ . '/../binaries');
            expect($this->manager->getInstallPath())->to->equal($path);
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

class TestDecompressor implements BinaryDecompressorInterface
{
    public $decompressedPath;

    public $targetPath;

    public function setTargetPath($path)
    {
        $this->targetPath = $path;
    }

    public function extract($compressedFilePath, $directory)
    {
        $this->decompressedPath = $compressedFilePath;
        file_put_contents($this->targetPath, 'binarydata');
    }
}
