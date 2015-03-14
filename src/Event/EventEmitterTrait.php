<?php
namespace Peridot\WebDriverManager\Event;

use Evenement\EventEmitterTrait as BaseEmitterTrait;
use Evenement\EventEmitterInterface as BaseEmitterInterface;

/**
 * EventEmitterTrait is used to implement EventEmitterInterface->inherit()
 *
 * @package Peridot\WebDriverManager\Event
 */
trait EventEmitterTrait
{
    use BaseEmitterTrait;

    public function inherit(array $events, BaseEmitterInterface $emitter)
    {
        foreach ($events as $event) {
            $emitter->on($event, function () use ($event) {
                $this->emit($event, func_get_args());
            });
        }
    }
} 
