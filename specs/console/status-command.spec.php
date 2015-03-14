<?php
use Peridot\WebDriverManager\Console\StatusCommand;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

describe('StatusCommand', function () {
    beforeEach(function () {
        $this->application = new Application();
        $this->manager = $this->getProphet()->prophesize('Peridot\WebDriverManager\Manager');
        $this->application->add(new StatusCommand($this->manager->reveal()));
    });

    afterEach(function () {
        $this->getProphet()->checkPredictions();
    });

    describe('->execute()', function () {
        beforeEach(function () {
            $this->binary = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\BinaryInterface');
            $this->binary->getName()->willReturn('selenium');
            $this->manager->getBinaries()->willReturn(['selenium' => $this->binary]);
            $this->manager->getInstallPath()->shouldBeCalled();
        });

        context('when binary is up to date', function () {
            beforeEach(function () {
                $this->binary->exists(Argument::any())->willReturn(true);
                $this->binary->isOutOfDate(Argument::any())->willReturn(false);
            });

            it('should report that the binary is up to date', function () {
                $command = $this->application->find('status');
                $tester = new CommandTester($command);
                $tester->execute(['command' => $command->getName()]);
                expect($tester->getDisplay())->to->match('/selenium is up to date/');
            });
        });

        context('when binary is outdated', function () {
            beforeEach(function () {
                $this->binary->exists(Argument::any())->willReturn(false);
                $this->binary->isOutOfDate(Argument::any())->willReturn(true);
            });

            it('should report that the binary is outdated', function () {
                $command = $this->application->find('status');
                $tester = new CommandTester($command);
                $tester->execute(['command' => $command->getName()]);
                expect($tester->getDisplay())->to->match('/selenium needs to be updated/');
            });
        });

        context('when binary is not present', function () {
            beforeEach(function () {
                $this->binary->exists(Argument::any())->willReturn(false);
                $this->binary->isOutOfDate(Argument::any())->willReturn(false);
            });

            it('should report that the binary is not present', function () {
                $command = $this->application->find('status');
                $tester = new CommandTester($command);
                $tester->execute(['command' => $command->getName()]);
                expect($tester->getDisplay())->to->match('/selenium is not present/');
            });
        });
    });
});
