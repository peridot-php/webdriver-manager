<?php
namespace Peridot\WebDriverManager\Console;

use Peridot\WebDriverManager\Manager;
use Symfony\Component\Console\Command\Command;

abstract class AbstractManagerCommand extends Command
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        parent::__construct(null);
        $this->manager = $manager;
    }
} 
