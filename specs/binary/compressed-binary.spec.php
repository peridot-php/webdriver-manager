<?php
use Peridot\WebDriverManager\Binary\CompressedBinary;

describe('CompressedBinary', function () {
    beforeEach(function () {
        $this->request = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\BinaryRequestInterface');
        $this->decompressor = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\BinaryDecompressorInterface');
        $this->binary = new TestCompressedBinary($this->request->reveal(), $this->decompressor->reveal());
        $this->request->request($this->binary->getUrl())->willReturn('string');

        $fixture = __DIR__ . '/' . $this->binary->getFileName();
        if (file_exists($fixture)) {
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

        it('write the zip contents to a temp file', function () {
            $this->binary->save(__DIR__);
            $this->decompressor->extract(\Prophecy\Argument::type('string'), __DIR__)->shouldBeCalled();
        });

        it('should return true if decompression succeeds', function () {
            $this->decompressor->extract(\Prophecy\Argument::type('string'), __DIR__)->willReturn(true);
            $result = $this->binary->save(__DIR__);
            expect($result)->to->be->true;
        });

        it('should return false if decompression fails', function () {
            $this->decompressor->extract(\Prophecy\Argument::type('string'), __DIR__)->willReturn(false);
            $result = $this->binary->save(__DIR__);
            expect($result)->to->be->false;
        });
    });

    describe('->fetchAndSave()', function () {
        it('should fetch and save contents', function () {
            $this->decompressor->extract(\Prophecy\Argument::type('string'), __DIR__)->willReturn(true);
            $result = $this->binary->fetchAndSave(__DIR__);
            expect($result)->to->be->true;
        });
    });
});

class TestCompressedBinary extends CompressedBinary
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getFileName()
    {
        return 'test-compressed-binary.zip';
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getUrl()
    {
        return 'http://url.com';
    }
}
