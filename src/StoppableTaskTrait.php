<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;

trait StoppableTaskTrait
{
    protected $stopPropagation = false;

    public function isPropagationStopped() : bool
    {
        return $this->stopPropagation;
    }
}
