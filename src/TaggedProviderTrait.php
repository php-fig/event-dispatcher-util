<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * A Tagged Provider allows for routing based on a "tag method" of Events.
 *
 * This trait demonstrates and provides a way to map events to listeners based
 * on more than the Event type.  Specifically, it allows designation of a "tag method"
 * on an Event that returns a single string.  Listeners may be registered only on that
 * tag.  Examples here include form-instance-specific listeners (where the tag method
 * would return a form name or form ID), workflow steps (where the tag method returns a
 * workflow name or workflow step name), or any other case where the Event filter is
 * based on user-defined configuration.
 *
 * This trait does not handle registration, as that is explicitly left up to the implementer.
 * Provider classes using this trait need to fill in the eventType() and tagMethod() methods
 * (using whatever logic they choose, including passed in by constructor).
 *
 * @see ListenerProviderInterface
 */
trait TaggedProviderTrait
{
    /**
     * Returns the class or interface type this provider is for.
     *
     * Any event not of this type will be skipped (return
     * no listeners).  The class or interface specified needs to
     * guarantee the presence of the method returned by tagMethod().
     *
     * @return string
     *   The class or interface type that this Provider is for.
     */
    abstract protected function eventType() : string;

    /**
     * Returns the method to call on an event to get its tag.
     *
     * The tag is an opaque string that identifies the event
     * within the scope of its type.  Examples include a form name,
     * workflow name, or some other user-configured object.
     *
     * @return string
     *   The tag method to call on an event.
     */
    abstract protected function tagMethod(): string;

    /**
     * Returns an ordered iterable of Listeners relevant to this tag.
     *
     * Not all Listeners returned by this method will be invoked. Only
     * those that are type-compatible with the Event being processed
     * will be.
     *
     * @param string $tag
     *   The tag for which we want relevant Listeners.
     * @return iterable
     *   An ordered iterable of candidate Listeners.  The key of each entry
     *   is the Event type the Listener is for.  (It will also apply if the
     *   $type is a parent of the Event's type.)  The value is an iterable
     *   of Listeners that apply to that type and to the tag that was passed.
     */
    abstract protected function getListenersForTag(string $tag) : iterable;

    /**
     * Returns an ordered iterable of Listeners relevant to all tags.
     *
     * Not all Listeners returned by this method will be invoked. Only
     * those that are type-compatible with the Event being processed
     * will be.
     *
     * Note that this method is called after getListenersForTag(), so
     * tag-specific Listeners will always be invoked first.
     *
     * @return iterable
     *   An ordered iterable of candidate Listeners.  The key of each entry
     *   is the Event type the Listener is for.  (It will also apply if the
     *   $type is a parent of the Event's type.)  The value is an iterable
     *   of Listeners that apply to that type.
     */
    abstract protected function getListenersForAllTags() : iterable;

    public function getListenersForEvent(object $event) : iterable
    {
        $eventType = $this->eventType();
        if (!$event instanceof $eventType) {
            return [];
        }

        $tag = $event->{$this->tagMethod()}();

        yield from $this->filterListenersForEvent($event, $this->getListenersForTag($tag));
        yield from $this->filterListenersForEvent($event, $this->getListenersForAllTags());
    }

    /**
     * @param object $event
     *   The Event to match against.
     * @param iterable $listenerSet
     *   An iterable in the format returned by getListenersForTag()/getListenersForAllTags().
     * @return iterable
     *   An iterable of listeners to be called.
     */
    protected function filterListenersForEvent(object $event, iterable $listenerSet) : iterable
    {
        foreach ($listenerSet as $type => $listeners) {
            if ($event instanceof $type) {
                yield from $listeners;
            }
        }
    }
}
