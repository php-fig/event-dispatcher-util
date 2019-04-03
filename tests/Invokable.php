<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;

class Invokable
{
    public function __invoke(Foo $event)
    {
        // do nothing
    }
}