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
