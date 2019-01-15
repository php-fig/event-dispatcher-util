<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;

/**
 * Utility trait for flagged Stoppable Events.
 *
 * Use this trait if you have a Stoppable Event and you want to track
 * whether or not it is stopped via a boolean flag property. Then set
 * that property whenever is appropriate for your application.
 *
 * If your event does not use an explicit flag to track whether or not
 * it should stop propagating then this trait is of no use to you.
 */
trait StoppableEventTrait
{
    protected $stopPropagation = false;

    public function isPropagationStopped() : bool
    {
        return $this->stopPropagation;
    }
}
