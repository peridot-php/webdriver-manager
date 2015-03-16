<?php
use Peridot\WebDriverManager\Console\StartCommand;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

describe('StartCommand', function () {
    beforeEach(function () {
        $this->application = new Application();
        $this->manager = $this->getProphet()->prophesize('Peridot\WebDriverManager\Manager');

        $this->update = $this->getProphet()->prophesize('Peridot\WebDriverManager\Console\UpdateCommand');
        $this->update->getName()->willReturn('update');
        $this->update->setApplication($this->application)->shouldBeCalled();
        $this->update->isEnabled()->willReturn(true);
        $this->update->getDefinition()->willReturn($this->application->getDefinition());
        $this->update->getAliases()->willReturn([]);

        $this->application->add(new StartCommand($this->manager->reveal()));
        $this->application->add($this->update->reveal());
    });

    afterEach(function () {
        $this->getProphet()->checkPredictions();
    });

    describe('->execute()', function () {
        it('should update and start', function () {
            $this->update->run(Argument::any(), Argument::any())->shouldBeCalled();
            $this->manager->start(false, 4444)->shouldBeCalled();

            $command = $this->application->find('start');
            $tester = new CommandTester($command);
            $tester->execute(['command' => $command->getName()]);
            expect($tester->getDisplay())->to->match('/Starting Selenium Server/');
        });

        context('when port is supplied', function () {
            it('should use the specified port if available', function () {
                $this->update->run(Argument::any(), Argument::any())->shouldBeCalled();
                $this->manager->start(false, 9000)->shouldBeCalled();

                $command = $this->application->find('start');
                $tester = new CommandTester($command);
                $tester->execute(['command' => $command->getName(), 'port' => 9000]);
            });
        });
    });
});
