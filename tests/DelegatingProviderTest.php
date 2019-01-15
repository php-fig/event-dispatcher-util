<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;

class DelegatingProviderTest extends TestCase
{

    public function test_dedicated_provider_blocks_default() : void
    {
        $specific = new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield function (CollectingEvent $event) { $event->add('A'); };
            }
        };
        $default = new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield function (CollectingEvent $event) { $event->add('B'); };
            }
        };

        $p = new DelegatingProvider();
        $p->setDefaultProvider($default);
        $p->addProvider($specific, [CollectingEvent::class]);

        $event = new CollectingEvent();
        foreach ($p->getListenersForEvent($event) as $listener) {
            $listener($event);
        }
        $this->assertEquals('A', implode($event->result()));
    }

    public function test_dedicated_provider_unused_goes_to_default() : void
    {
        $specific = new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                return [];
            }
        };
        $default = new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield function (CollectingEvent $event) { $event->add('B'); };
            }
        };

        $p = new DelegatingProvider();
        $p->setDefaultProvider($default);
        $p->addProvider($specific, [DoesNotExist::class]);

        $event = new CollectingEvent();
        foreach ($p->getListenersForEvent($event) as $listener) {
            $listener($event);
        }
        $this->assertEquals('B', implode($event->result()));
    }


}
