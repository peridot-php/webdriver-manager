<?php
use Peridot\WebDriverManager\Binary\BinaryResolver;
use Peridot\WebDriverManager\Binary\SeleniumStandalone;
use Peridot\WebDriverManager\Versions;

describe('SeleniumStandalone', function () {
    describe('->isOutOfDate()', function () {
        beforeEach(function () {
            $this->binary = new SeleniumStandalone(new BinaryResolver());
            $this->current = __DIR__ . '/' . $this->binary->getFileName();
            $this->old = str_replace(Versions::SELENIUM, '0.1.1', $this->current);
        });

        require 'shared/is-out-of-date.php';
    });
});
