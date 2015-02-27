<?php
use Peridot\WebDriverManager\Manager;

describe('Manager', function () {
    beforeEach(function () {
        $this->manager = new Manager();
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
