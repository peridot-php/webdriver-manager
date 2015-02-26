<?php
use Peridot\WebDriverManager\Binary\StandardBinaryRequest;
use Peridot\WebDriverManager\Manager;

describe('Manager', function () {
    beforeEach(function () {
        $this->manager = new Manager();
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
            expect($manager->getBinaryRequest())->to->be->an->instanceof('Peridot\WebDriverManager\Binary\StandardBinaryRequest');
        });

        it('should return the BinaryRequestInterface if given', function () {
            $request = new StandardBinaryRequest();
            $manager = new Manager($request);
            expect($manager->getBinaryRequest())->to->equal($request);
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
                expect($selenium)->to->have->length(1);
                expect($chrome)->to->have->length(1);
            });
        });
    });
});
