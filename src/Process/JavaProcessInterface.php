<?php
namespace Peridot\WebDriverManager\Process;

interface JavaProcessInterface
{
    /**
     * Returns whether or not java is available for use.
     *
     * @return bool
     */
    public function isAvailable();
} 
