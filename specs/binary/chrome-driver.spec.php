<?php
use Peridot\WebDriverManager\Binary\BinaryResolver;
use Peridot\WebDriverManager\Binary\ChromeDriver;
use Peridot\WebDriverManager\Versions;

describe('ChromeDriver', function () {
    describe('->isOutOfDate()', function () {
        beforeEach(function () {
            $this->binary = new ChromeDriver(new BinaryResolver());
            $this->current = __DIR__ . '/' . $this->binary->getOutputFileName();
            $this->old = str_replace(Versions::CHROMEDRIVER, '0.1.1', $this->current);
        });

        require 'shared/is-out-of-date.php';
    });
});
