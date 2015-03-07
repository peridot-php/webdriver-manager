<?php
use Peridot\WebDriverManager\Binary\BinaryResolver;
use Peridot\WebDriverManager\Binary\Decompression\ZipDecompressor;
use Peridot\WebDriverManager\Binary\Request\StandardBinaryRequest;
use Peridot\WebDriverManager\OS\System;
use Peridot\WebDriverManager\Test\TestDecompressor;
use Prophecy\Argument;

describe('BinaryResolver', function () {
    beforeEach(function () {
        $this->request = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\Request\BinaryRequestInterface');
        $this->decompressor = new TestDecompressor();
        $this->system = $this->getProphet()->prophesize('Peridot\WebDriverManager\OS\SystemInterface');

        $this->resolver = new BinaryResolver(
            $this->request->reveal(),
            $this->decompressor,
            $this->system->reveal()
        );

        $this->request->request(Argument::any())->willReturn('string');
    });

    describe('->getInstallPath()', function () {
        it('should return a default path', function () {
            $path = realpath(__DIR__ . '/../../binaries');
            expect($this->resolver->getInstallPath())->to->equal($path);
        });
    });

    describe('->getBinaryRequest()', function () {
        it('should return a StandardBinaryRequest by default', function () {
            $resolver = new BinaryResolver();
            expect($resolver->getBinaryRequest())->to->be->an->instanceof('Peridot\WebDriverManager\Binary\Request\StandardBinaryRequest');
        });

        it('should return the BinaryRequestInterface if given', function () {
            $request = new StandardBinaryRequest();
            $resolver = new BinaryResolver($request);
            expect($resolver->getBinaryRequest())->to->equal($request);
        });
    });

    describe('->getBinaryDecompressor()', function () {
        it('should return a ZipDecompressor by default', function () {
            $resolver = new BinaryResolver();
            expect($resolver->getBinaryDecompressor())->to->be->an->instanceof('Peridot\WebDriverManager\Binary\Decompression\ZipDecompressor');
        });

        it('should return the BinaryDecompressorInterface if given', function () {
            $decompressor = new ZipDecompressor();
            $resolver = new BinaryResolver(null, $decompressor);
            expect($resolver->getBinaryDecompressor())->to->equal($decompressor);
        });
    });

    describe('->getSystem()', function () {
        it('should return a System by default', function () {
            $resolver = new BinaryResolver();
            expect($resolver->getSystem())->to->be->an->instanceof('Peridot\WebDriverManager\OS\System');
        });

        it('should return the SystemInterface if given', function () {
            $system = new System();
            $resolver = new BinaryResolver(null, null, $system);
            expect($resolver->getSystem())->to->equal($system);
        });
    });
});
