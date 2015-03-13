<?php
use Peridot\WebDriverManager\Console\StartCommand;
use Peridot\WebDriverManager\Console\UpdateCommand;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

describe('StartCommand', function () {
    beforeEach(function () {
        $this->application = new Application();
        $this->manager = $this->getProphet()->prophesize('Peridot\WebDriverManager\Manager');
        $this->application->add(new StartCommand($this->manager->reveal()));
        $this->application->add(new UpdateCommand($this->manager->reveal()));
    });

    afterEach(function () {
        $this->getProphet()->checkPredictions();
    });

    describe('->execute()', function () {
        it('should update and start', function () {
            $command = $this->application->find('start');
            $tester = new CommandTester($command);
            $tester->execute(['command' => $command->getName()]);
            expect($tester->getDisplay())->to->match('/Starting Selenium Server/');
            $this->manager->update(Argument::any())->shouldBeCalled();
            $this->manager->start(false, 4444)->shouldBeCalled();
        });

        context('when port is supplied', function () {
            it('should use the specified port if available', function () {
                $command = $this->application->find('start');
                $tester = new CommandTester($command);
                $tester->execute(['command' => $command->getName(), 'port' => 9000]);
                $this->manager->update(Argument::any())->shouldBeCalled();
                $this->manager->start(false, 9000)->shouldBeCalled();
            });
        });
    });
});
