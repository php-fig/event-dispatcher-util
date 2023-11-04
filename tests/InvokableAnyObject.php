<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;

class InvokableAnyObject
{
    public function __invoke(object $event)
    {
        // do nothing
    }
}
