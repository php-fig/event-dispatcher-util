<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;

class Invokeable
{
    public function __invoke(Foo $event)
    {
        // do nothing
    }
}