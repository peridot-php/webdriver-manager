#WebDriver Manager

[![Build Status](https://travis-ci.org/peridot-php/webdriver-manager.png)](https://travis-ci.org/peridot-php/webdriver-manager)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/peridot-php/webdriver-manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/peridot-php/webdriver-manager/?branch=master)

The perfect companion for projects with functional tests. Heavily inspired by the webdriver manager that ships with [protractor](https://github.com/angular/protractor). WebDriver Manager allows you to keep Selenium Server binaries up to date as well as a packaged solution for easily starting Selenium Server up.

In addition to an easy to use command line application, WebDriver Manager provides a library for managing Selenium binaries in your own apps and tools.

##Usage

![WebDriver Manager Usage](https://raw.github.com/peridot-php/webdriver-manager/master/img/usage.png "WebDriver Manager Usage")

###clean

Remove all installed binaries.

![WebDriver clean command](https://raw.github.com/peridot-php/webdriver-manager/master/img/clean.png "WebDriver clean command")

###status

List all available binaries and the status. Status shows if the binary is installed, out of date, or missing.

 ![WebDriver status command](https://raw.github.com/peridot-php/webdriver-manager/master/img/status.png "WebDriver status command")