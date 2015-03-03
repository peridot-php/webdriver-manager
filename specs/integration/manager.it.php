<?php
use Peridot\WebDriverManager\Manager;

describe('Manager', function () {
    beforeEach(function () {
        $this->system = $this->getProphet()->prophesize('Peridot\WebDriverManager\OS\SystemInterface');
        $this->manager = new Manager(null, null, $this->system->reveal());

        $selenium = glob($this->manager->getInstallPath() . '/selenium*');
        $chrome = glob($this->manager->getInstallPath() . '/chrome*');
        $fixtures = array_merge($selenium, $chrome);

        foreach ($fixtures as $fixture) {
            unlink($fixture);
        }
    });

    describe('->update()', function () {
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
            });

            context('and it is 32 bit linux', function () {
                beforeEach(function () {
                    $this->system->is64Bit()->willReturn(false);
                });

                require 'shared/manager-update.php';
            });

            context('and it is 64 bit linux', function () {
                beforeEach(function () {
                    $this->system->is64Bit()->willReturn(true);
                });

                require 'shared/manager-update.php';
            });
        });
    });
});
