<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;


use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * An aggregate provider encapsulates multiple other providers and concatenates their responses.
 *
 * Be aware that any ordering of listeners in different sub-providers is ignored, and providers are
 * iterated in the order in which they were added.  That is, all listeners from the first provider
 * added will be returned to the caller, then all listeners from the second provider, and so on.
 */
class AggregateProvider implements ListenerProviderInterface
{
    /**
     * @var array
     */
    protected $providers = [];

    public function getListenersForEvent(object $event): iterable
    {
        /** @var ListenerProviderInterface $provider */
        foreach ($this->providers as $provider) {
            yield from $provider->getListenersForEvent($event);
        }
    }

    /**
     * Enqueues a listener provider to this set.
     *
     * @param ListenerProviderInterface $provider
     *   The provider to add.
     * @return AggregateProvider
     *   The called object.
     */
    public function addProvider(ListenerProviderInterface $provider) : self
    {
        $this->providers[] = $provider;
        return $this;
    }
}
