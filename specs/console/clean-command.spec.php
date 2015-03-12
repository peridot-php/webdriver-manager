<?php
use Peridot\WebDriverManager\Console\CleanCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

describe('CleanCommand', function () {
    beforeEach(function () {
        $this->application = new Application();
        $this->manager = $this->getProphet()->prophesize('Peridot\WebDriverManager\Manager');
        $this->application->add(new CleanCommand($this->manager->reveal()));
    });

    afterEach(function () {
        $this->getProphet()->checkPredictions();
    });

    describe('->execute()', function () {
        it('should delete binaries', function () {
            $command = $this->application->find('clean');
            $tester = new CommandTester($command);
            $this->manager->clean()->shouldBeCalled();
            $tester->execute(['command' => $command->getName()]);
            expect($tester->getDisplay())->to->match('/Deleting installed binaries/');
            expect($tester->getDisplay())->to->match('/Installation directory cleaned/');
        });
    });
});
