<?php
use Peridot\WebDriverManager\Manager;
use Peridot\WebDriverManager\Process\SeleniumProcess;

describe('SeleniumProcess', function () {
    beforeEach(function () {
        $this->process = new SeleniumProcess();
        $this->manager = new Manager();
        $files = glob($this->manager->getInstallPath() . '/*');
        foreach ($files as $file) {
            unlink($file);
        }
    });

    it('checks java availability', function () {
        expect($this->process->isAvailable())->to->be->true;
    });

    it('runs a process in the background', function () {
        $this->manager->updateSingle('selenium');
        $binaries = $this->manager->getBinaries();
        $this->process->addBinary($binaries['selenium'], $this->manager->getInstallPath());
        $this->process->start(true);
        usleep(250000);

        if (! $this->process->isRunning()) {
            throw new Exception($this->process->getCommand() . ' ' . $this->process->getError());
        }

        $this->process->close();
    });
});
