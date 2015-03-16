<?php
use Peridot\WebDriverManager\Binary\BinaryResolver;
use Peridot\WebDriverManager\Binary\SeleniumStandalone;
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

    beforeEach(function () { //mock the manager
        $binaries = ['selenium' => new SeleniumStandalone(new BinaryResolver())];

        $this->manager->getBinaries()->willReturn($binaries);
        $this->manager->getPendingBinaries()->willReturn($binaries);
        $this->manager->on('request.start', Argument::any())->shouldBeCalled();
        $this->manager->on('progress', Argument::any())->shouldBeCalled();
        $this->manager->on('complete', Argument::any())->shouldBeCalled();
        $this->manager->update(Argument::any())->shouldBeCalled();
        $this->manager->getInstallPath()->shouldBeCalled();
    });

    afterEach(function () {
        $this->getProphet()->checkPredictions();
    });

    describe('->execute()', function () {
        it('should update and start', function () {
            $this->manager->update(Argument::any())->shouldBeCalled();
            $this->manager->start(false, 4444)->shouldBeCalled();

            $command = $this->application->find('start');
            $tester = new CommandTester($command);
            $tester->execute(['command' => $command->getName()]);
            expect($tester->getDisplay())->to->match('/Starting Selenium Server/');
        });

        context('when port is supplied', function () {
            it('should use the specified port if available', function () {
                $this->manager->update(Argument::any())->shouldBeCalled();
                $this->manager->start(false, 9000)->shouldBeCalled();

                $command = $this->application->find('start');
                $tester = new CommandTester($command);
                $tester->execute(['command' => $command->getName(), 'port' => 9000]);
            });
        });
    });
});
