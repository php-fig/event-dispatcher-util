<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * A Tagged Provider allows for routing based on a "tag method" of events.
 */
class TaggedProvider implements ListenerProviderInterface
{
    use ParameterDeriverTrait;

    /** @var array */
    protected $listeners = [];

    /** @var array */
    protected $all = [];

    /** @var string */
    protected $eventType;

    /** @var string */
    protected $tagMethod;

    public function __construct(string $eventType, string $tagMethod)
    {
        $this->eventType = $eventType;
        $this->tagMethod = $tagMethod;
    }

    public function getListenersForEvent(object $event) : iterable
    {
        if (!$event instanceof $this->eventType) {
            return [];
        }

        $tag = $event->{$this->tagMethod}();

        foreach ($this->listeners[$tag] as $type => $listeners) {
            foreach ($listeners as $listener) {
                if ($event instanceof $type) {
                    yield $listener;
                }
            }
        }
        foreach ($this->all as $type => $listeners) {
            foreach ($listeners as $listener) {
                if ($event instanceof $type) {
                    yield $listener;
                }
            }
        }
    }

    public function addListener(callable $listener, string $tagName = '', string $type = null) : void
    {
        $type = $type ?? $this->getParameterType($listener);

        if ($tagName) {
            $this->listeners[$tagName][$type][] = $listener;
        }
        else {
            $this->all[$type][] = $listener;
        }
    }

}
