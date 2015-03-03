<?php
use Peridot\WebDriverManager\Versions;

beforeEach(function () {
    $path = $this->manager->getInstallPath();
    file_put_contents("$path/selenium-server-standalone-2.44.0.jar", 'jarjar');
    file_put_contents("$path/chromedriver_2.13.zip", 'zipzip');
});

it('should download the latest selenium standalone and chrome driver', function () {
    $this->manager->update();
    $path = $this->manager->getInstallPath();
    $seleniums = glob("$path/selenium-server-standalone-*");
    $chromes = glob("$path/chromedriver");
    expect($seleniums)->to->have->length->of->at->least(1, 'no selenium file found');
    expect($chromes)->to->have->length->of->at->least(1, 'no chrome file found');
});

it('should be able to update a binary by name', function () {
    $this->system->is64Bit()->shouldNotBecalled(); //ignore chromedriver for this test
    $this->manager->update('selenium');
    $path = $this->manager->getInstallPath();
    $selenium = Versions::SELENIUM;
    $chrome = Versions::CHROMEDRIVER;
    $seleniums = glob("$path/selenium-server-standalone-$selenium*");
    $chromes = glob("$path/chromedriver_$chrome*");
    expect($seleniums)->to->have->length->of->at->least(1);
    expect($chromes)->to->have->length(0);
});

it('should throw an exception if binary name is not found', function () {
    //os calls should be ignored
    $this->system->isMac()->shouldNotBecalled();
    $this->system->isLinux()->shouldNotBecalled();
    $this->system->isWindows()->shouldNotBecalled();
    $this->system->is64Bit()->shouldNotBecalled();

    expect([$this->manager, 'update'])->with('nope')->to->throw('RuntimeException');
});

it('should replace old versions with new versions', function () {
    $chrome = Versions::CHROMEDRIVER;
    $selenium = Versions::SELENIUM;
    $this->manager->update();
    $path = $this->manager->getInstallPath();

    $newSeleniums = glob("$path/selenium-server-standalone-$selenium*");
    $newChromes = glob("$path/chromedriver_$chrome.zip");
    $oldSeleniums = glob("$path/selenium-server-standalone-2.44.0*");
    $oldChromes = glob("$path/chromedriver_2.13.zip");

    expect($newSeleniums)->to->have->length(1, 'no seleniums found');
    expect($newChromes)->to->have->length(1, 'no chromes found');
    expect($oldSeleniums)->to->have->length(0, 'old seleniums found');
    expect($oldChromes)->to->have->length(0, 'old chromes found');
});
