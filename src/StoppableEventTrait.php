<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;

trait StoppableEventTrait
{
    protected $stopPropagation = false;

    public function isPropagationStopped() : bool
    {
        return $this->stopPropagation;
    }
}
