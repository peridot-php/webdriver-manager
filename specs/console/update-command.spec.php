<?php
use Peridot\WebDriverManager\Console\UpdateCommand;
use Peridot\WebDriverManager\Manager;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

require 'BinaryHelper.php';

describe('UpdateCommand', function () {

    $helper = null;

    beforeEach(function () use (&$helper) {
        $this->application = new Application();
        $this->manager = $this->getProphet()->prophesize('Peridot\WebDriverManager\Manager');
        $this->manager->getInstallPath()->willReturn(__DIR__);
        $this->application->add(new UpdateCommand($this->manager->reveal()));

        if ($helper === null) {
            $helper = new BinaryHelper($this->getProphet());
            $this->peridotAddChildScope(new BinaryHelper($this->getProphet()));
        }
    });

    describe('->execute()', function () {
        beforeEach(function () {
            $this->manager->on('request.start', Argument::any())->shouldBeCalled();
            $this->manager->on('progress', Argument::any())->shouldBeCalled();
            $this->manager->on('complete', Argument::any())->shouldBeCalled();
            $this->manager->update(Argument::any())->shouldBeCalled();
        });

        it('should update a single binary if the name is given', function () {
            $command = $this->application->find('update');
            $tester = new CommandTester($command);


            $binary = $this->createBinary('binary', true, false);

            $this->manager->getBinaries(Argument::any())->willReturn(['binary' => $binary->reveal()]);
            $tester->execute(['command' => $command->getName(), 'name' => 'binary']);
            expect($tester->getDisplay())->to->match('/Updating binary/');
        });

        it('should notify the user if the binary is not supported', function () {
            $command = $this->application->find('update');
            $tester = new CommandTester($command);

            $binary = $this->createBinary('binary', false, false);

            $this->manager->getBinaries(Argument::any())->willReturn(['binary' => $binary->reveal()]);
            $tester->execute(['command' => $command->getName(), 'name' => 'binary']);
            expect($tester->getDisplay())->to->match('/binary is not supported by your system/');
        });

        it('should include progress', function () {
            $application = new Application();
            $application->add(new UpdateCommand(new CannedManager()));
            $command = $application->find('update');
            $tester = new CommandTester($command);
            $tester->execute(['command' => $command->getName()]);
            expect($tester->getDisplay())->to->match('/100%/');
            expect($tester->getDisplay())->to->match('/Downloading/');
        });

        //it('should include a ')
    });
});

class CannedManager extends Manager
{
    public function update($binaryName = '')
    {
        $this->emit('request.start', ['http://a.b', 100]);
        $this->emit('progress', [1]);
        $this->emit('complete');
    }

}
