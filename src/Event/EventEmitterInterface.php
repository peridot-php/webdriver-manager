<?php
namespace Peridot\WebDriverManager\Event;
use Evenement\EventEmitterInterface as BaseEmitterInterface;

interface EventEmitterInterface extends BaseEmitterInterface
{
    /**
     * Inherit event listeners from another event emitter.
     *
     * @param array $events an array of event names
     * @param BaseEmitterInterface $emitter
     * @return void
     */
    public function inherit(array $events, BaseEmitterInterface $emitter);
} 
