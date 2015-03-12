<?php
use Peridot\WebDriverManager\Console\UpdateCommand;
use Peridot\WebDriverManager\Manager;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

describe('UpdateCommand', function () {
    beforeEach(function () {
        $this->application = new Application();
        $this->manager = $this->getProphet()->prophesize('Peridot\WebDriverManager\Manager');
        $this->application->add(new UpdateCommand($this->manager->reveal()));
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
            $binary = $this->getProphet()->prophesize('Peridot\WebDriverManager\Binary\BinaryInterface');
            $binary->getName()->willReturn('binary');

            $this->manager->getBinaries()->willReturn(['binary' => $binary->reveal()]);
            $tester->execute(['command' => $command->getName(), 'name' => 'binary']);
            expect($tester->getDisplay())->to->match('/Updating binary/');
        });

        it('should include progress', function () {
            $application = new Application();
            $application->add(new UpdateCommand(new CannedManager()));
            $command = $application->find('update');
            $tester = new CommandTester($command);
            $tester->execute(['command' => $command->getName()]);
            expect($tester->getDisplay())->to->match('/100%/');
            expect($tester->getDisplay())->to->match('/Finished downloading/');
        });
    });
});

class CannedManager extends Manager
{
    public function update($binaryName = '')
    {
        $this->emit('request.start', [100]);
        $this->emit('progress', [1]);
        $this->emit('complete', ['http://a.b']);
    }

}
