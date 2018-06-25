<?php
use Peridot\WebDriverManager\Binary\BinaryResolver;
use Peridot\WebDriverManager\Manager;
use Peridot\WebDriverManager\Binary\ChromeDriver;

describe('Manager', function () {
    beforeEach(function () {
        $this->system = $this->getProphet()->prophesize('Peridot\WebDriverManager\OS\SystemInterface');
        $resolver = new BinaryResolver(null, null, $this->system->reveal());
        $this->manager = new Manager($resolver);

        $selenium = glob($this->manager->getInstallPath() . '/selenium*');
        $chrome = glob($this->manager->getInstallPath() . '/chrome*');
        $ie = glob($this->manager->getInstallPath() . '/IE*');
        $fixtures = array_merge($selenium, $chrome, $ie);

        foreach ($fixtures as $fixture) {
            unlink($fixture);
        }
    });

    describe('->start()', function () {
        it('should support arbitrary arguments', function () {
            $manager = new Manager();
            $manager->updateSingle('selenium');
            $log = tempnam(sys_get_temp_dir(), 'SEL_');
            $proc = $manager->start(true, 4444, ['-log', $log]);
            usleep(500000);
            $proc->close();

            $contents = file_get_contents($log);
            expect($contents)->to->not->be->empty;
        });
    });

    describe('->update()', function () {
        it('should update a single binary', function () {
            $this->manager->update('selenium');
            $path = $this->manager->getInstallPath();
            $selenium = glob("$path/selenium-server-standalone-*");
            $chrome = glob("$path/chromedriver*");
            expect($selenium)->to->have->length(1, 'no selenium file found');
            expect($chrome)->to->have->length(0, 'chrome files found');
        });

        context('when on a mac operating system', function () {
            beforeEach(function () {
                $this->system->isMac()->willReturn(true);
                $this->system->isWindows()->willReturn(false);
                $this->system->isLinux()->willReturn(false);
                $this->system->is64Bit()->willReturn(true);
            });

            require 'shared/manager-update.php';
        });

        context('when on a windows operating system', function () {
            beforeEach(function () {
                $this->system->isMac()->willReturn(false);
                $this->system->isWindows()->willReturn(true);
                $this->system->isLinux()->willReturn(false);
            });

            context('and it is 32 bit windows', function () {
                beforeEach(function () {
                    $this->system->is64Bit()->willReturn(false);
                });

                it('should include windows binaries', function () {
                    $this->manager->updateSingle('IEDriver');
                    $path = $this->manager->getInstallPath();
                    $ie = glob("$path/IE*");
                    expect($ie)->have->length->of->at->least(1);
                });

                require 'shared/manager-update.php';
            });

            context('and it is 64 bit windows', function () {
                beforeEach(function () {
                    $this->system->is64Bit()->willReturn(true);
                });

                it('should include windows binaries', function () {
                    $this->manager->updateSingle('IEDriver');
                    $path = $this->manager->getInstallPath();
                    $ie = glob("$path/IE*");
                    expect($ie)->have->length->of->at->least(1);
                });

                require 'shared/manager-update.php';
            });
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

                it('should update if chrome driver exists for this version', function () {
                    $binary = new ChromeDriver(new BinaryResolver(null, null, $this->system->reveal()));
                    $context = stream_context_create(array(
                        'http' => array('ignore_errors' => true),
                    ));
                    if($result = file_get_contents($binary->getUrl(), false, $context)){
                        require 'shared/manager-update.php';
                    }else{
                        print("File not found for this version.".$binary->getUrl());
                    }
                });


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
