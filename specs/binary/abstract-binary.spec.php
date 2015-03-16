<?php
use Peridot\WebDriverManager\Binary\AbstractBinary;
use Peridot\WebDriverManager\OS\System;

describe('AbstractBinary', function () {
    beforeEach(function () {
        $this->resolver = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\BinaryResolverInterface');
        $this->binary = new TestBinary($this->resolver->reveal());
        $this->resolver->request($this->binary->getUrl())->willReturn('string');

        $fixture = __DIR__ . '/' . $this->binary->getFileName();
        if (file_exists($fixture)) {
            unlink($fixture);
        }
    });

    afterEach(function () {
        $this->getProphet()->checkPredictions();
    });

    describe('->fetch()', function () {
        it('should set the contents of the binary', function () {
            $this->binary->fetch();
            expect($this->binary->getContents())->to->equal('string');
        });
    });

    describe('->isSupported()', function () {
        it('returns true by default', function () {
            expect($this->binary->isSupported())->to->be->true;
        });
    });

    describe('->save()', function () {
        beforeEach(function () {
            $this->binary->fetch();
        });

        it('should write the contents to the directory', function () {
            $result = $this->binary->save(__DIR__);
            expect($result)->to->be->true;
        });

        it('should return false if there is no content', function () {
            $binary = new TestBinary($this->resolver->reveal());
            $result = $binary->save(__DIR__);
            expect($result)->to->be->false;
        });

        it('should return true if the current version is already installed', function () {
            $binary = new TestBinary($this->resolver->reveal());
            $binary->fetch();
            $binary->save(__DIR__);

            $binary = new TestBinary($this->resolver->reveal());
            expect($binary->save(__DIR__))->to->be->true;
        });
    });

    describe('->fetchAndSave()', function () {
        it('should fetch and save contents', function () {
            $result = $this->binary->fetchAndSave(__DIR__);
            expect($result)->to->be->true;
        });

        it('should return true if already installed and up to date', function () {
            $this->binary->fetchAndSave(__DIR__);

            $resolver = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\BinaryResolverInterface');
            $binary = new TestBinary($resolver->reveal());
            $resolver->request()->shouldNotBeCalled();
            expect($binary->fetchAndSave(__DIR__))->to->be->true;
        });
    });
});

class TestBinary extends AbstractBinary
{
    public function getName()
    {
        return 'test-binary';
    }

    public function getFileName()
    {
        return 'test-binary.txt';
    }

    public function getUrl()
    {
        return 'http://url.com';
    }

    /**
     * Return a pattern to identify old versions of a binary.
     *
     * @param string $directory
     * @return string
     */
    protected function getOldFilePattern($directory)
    {
        // TODO: Implement getOldFilePattern() method.
    }
}
