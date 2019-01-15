<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;


use PHPUnit\Framework\TestCase;

class StoppableEventTest extends TestCase
{

    public function test() : void
    {
        $event = new class {
            use StoppableEventTrait;

            public function stop() : void
            {
                $this->stopPropagation = true;
            }
        };

        $event->stop();

        $this->assertTrue($event->isPropagationStopped());

    }

}
