<?php
use Peridot\Scope\Scope;
use Prophecy\Argument;
use Prophecy\Prophet;

class BinaryHelper extends Scope
{
    /**
     * @var Prophet
     */
    private $prophet;

    public function __construct(Prophet $prophet)
    {
        $this->prophet = $prophet;
    }

    /**
     * Create a binary mock.
     *
     * @param $name
     * @param $supported
     * @param $exists
     */
    public function createBinary($name, $supported, $exists)
    {
        $binary = $this->prophet->prophesize('Peridot\WebDriverManager\Binary\BinaryInterface');
        $binary->isSupported()->willReturn($supported);
        $binary->getName()->willReturn($name);
        $binary->exists(Argument::any())->willReturn($exists);
        return $binary;
    }
} 
