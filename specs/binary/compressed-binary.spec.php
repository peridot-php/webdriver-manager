<?php
use Peridot\WebDriverManager\Binary\CompressedBinary;
use Prophecy\Argument;

describe('CompressedBinary', function () {
    beforeEach(function () {
        $this->resolver = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\BinaryResolverInterface');
        $this->binary = new TestCompressedBinary($this->resolver->reveal());
        $this->resolver->request($this->binary->getUrl())->willReturn('string');

        $fixtures = glob(__DIR__ . "/test-*");
        foreach ($fixtures as $fixture) {
            unlink($fixture);
        }
    });

    afterEach(function () {
        $this->getProphet()->checkPredictions();
    });

    describe('->save()', function () {
        beforeEach(function () {
            $this->binary->fetch();
        });

        it('should write the zip contents to an output file', function () {
            $this->resolver->extract(Argument::containingString($this->binary->getOutputFileName()), __DIR__)->shouldBeCalled();
            $this->binary->save(__DIR__);
        });

        it('should return true if decompression succeeds', function () {
            $this->resolver->extract(Argument::containingString($this->binary->getOutputFileName()), __DIR__)->willReturn(true);
            $result = $this->binary->save(__DIR__);
            expect($result)->to->be->true;
        });

        it('should return false if decompression fails', function () {
            $this->resolver->extract(Argument::containingString($this->binary->getOutputFileName()), __DIR__)->willReturn(false);
            $result = $this->binary->save(__DIR__);
            expect($result)->to->be->false;
        });

        context('when the current version is already installed', function () {
            beforeEach(function () {
                file_put_contents(__DIR__ . '/' . $this->binary->getOutputFileName(), 'zipzipzip');
            });

            it('should return true without unzipping', function () {
                $this->resolver->extract()->shouldNotBeCalled();
                $result = $this->binary->save(__DIR__);
                expect($result)->to->be->true;
            });
        });
    });

    describe('->fetchAndSave()', function () {
        it('should fetch and save contents', function () {
            $this->resolver->extract(Argument::containingString($this->binary->getOutputFileName()), __DIR__)->willReturn(true);
            $result = $this->binary->fetchAndSave(__DIR__);
            expect($result)->to->be->true;
        });

        it('should return true if already installed and up to date', function () {
            $this->resolver->extract(Argument::containingString($this->binary->getOutputFileName()), __DIR__)->willReturn(true);
            $this->binary->fetchAndSave(__DIR__);
            $resolver = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\BinaryResolverInterface');
            $binary = new TestCompressedBinary($resolver->reveal());
            expect($binary->fetchAndSave(__DIR__))->to->be->true;
        });
    });
});

class TestCompressedBinary extends CompressedBinary
{
    public function getName()
    {
        return 'test-compressed-binary';
    }

    public function getFileName()
    {
        return 'test-compressed-binary.zip';
    }

    public function getUrl()
    {
        return 'http://url.com';
    }

    public function getOutputFileName()
    {
        return 'test-compressed-output.zip';
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
