<?php
use Peridot\WebDriverManager\Binary\AbstractBinary;

describe('AbstractBinary', function () {
    beforeEach(function () {
        $this->request = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\BinaryRequestInterface');
        $this->binary = new TestBinary($this->request->reveal());
        $this->request->request($this->binary->getUrl())->willReturn('string');

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

    describe('->save()', function () {
        beforeEach(function () {
            $this->binary->fetch();
        });

        it('should write the contents to the directory', function () {
            $result = $this->binary->save(__DIR__);
            expect($result)->to->be->true;
        });

        it('should return false if there is no content', function () {
            $binary = new TestBinary($this->request->reveal());
            $result = $binary->save(__DIR__);
            expect($result)->to->be->false;
        });
    });

    describe('->fetchAndSave()', function () {
        it('should fetch and save contents', function () {
            $result = $this->binary->fetchAndSave(__DIR__);
            expect($result)->to->be->true;
        });
    });
});

class TestBinary extends AbstractBinary
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getFileName()
    {
        return 'test-binary.txt';
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
