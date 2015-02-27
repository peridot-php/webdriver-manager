<?php
use Peridot\WebDriverManager\Binary\Decompression\BinaryDecompressorInterface;
use Peridot\WebDriverManager\Binary\Decompression\ZipDecompressor;
use Peridot\WebDriverManager\Binary\Request\StandardBinaryRequest;
use Peridot\WebDriverManager\Manager;
use Prophecy\Argument;

describe('Manager', function () {
    beforeEach(function () {
        $this->request = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\Request\BinaryRequestInterface');
        $this->decompressor = new TestDecompressor();

        $this->manager = new Manager(
            $this->request->reveal(),
            $this->decompressor
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

    describe('->getInstallPath()', function () {
        it('should return a default path', function () {
            $path = realpath(__DIR__ . '/../binaries');
            expect($this->manager->getInstallPath())->to->equal($path);
        });
    });

    describe('->update()', function () {
        context('when on a mac operating system', function () {
            it('should download the latest selenium standalone and chrome driver', function () {
                $this->manager->update();
                $path = $this->manager->getInstallPath();
                $selenium = glob("$path/selenium-server-standalone-*");
                $chrome = glob("$path/chromedriver");
                expect($selenium)->to->have->length(1, 'no selenium file found');
                expect($chrome)->to->have->length(1, 'no chrome file found');
            });
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
