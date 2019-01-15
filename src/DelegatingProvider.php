<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;


use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * A Delegating provider.
 *
 * The delegating provider allows for selected types of event to be handled by dedicated
 * sub-providers, which if used will block the use of a default sub-provider.  That is,
 * certain high-frequency event types can be handled by dedicated providers and then
 * skip the normal lookup process of the default provider.  That can provide a performance
 * benefit if certain tasks are triggered many dozens of times or more. It also allows
 * for dedicated providers for certain types of event that have runtime sub-types (such as
 * a form event that also cares about a form ID) without polluting other providers with
 * the extra matching logic.
 *
 * Note: The presence of a sub-provider that wants to intercept a given type of event
 * will be sufficient to block the default from firing, even if it has no applicable
 * listeners.
 */
class DelegatingProvider implements ListenerProviderInterface
{

    /**
     * @var array
     *
     * An array of type to provider maps.  The keys are class name strings.
     * The values are an array of provider objects that should be called for that type.
     */
    protected $providers =[];

    /** @var ListenerProviderInterface */
    protected $defaultProvider;

    public function __construct(ListenerProviderInterface $defaultProvider = null)
    {
        if ($defaultProvider) {
            $this->defaultProvider = $defaultProvider;
        }
    }

    /**
     * Adds a provider that will be deferred to for the specified Event types.
     *
     * @param ListenerProviderInterface $provider
     *   The provider to which to defer.
     * @param array $types
     *   An array of class types. Any events matching these types will be delegated
     *   to the specified provider(s).
     * @return DelegatingProvider
     */
    public function addProvider(ListenerProviderInterface $provider, array $types) : self
    {
        foreach ($types as $type) {
            $this->providers[$type][] = $provider;
        }

        return $this;
    }

    /**
     * Sets the provider that will be deferred to for un-listed Event types.
     *
     * @param ListenerProviderInterface $provider
     *   The provider that should be called unless preempted by a dedicated provider.
     * @return self
     *   The called object
     */
    public function setDefaultProvider(ListenerProviderInterface $provider) : self
    {
        $this->defaultProvider = $provider;

        return $this;
    }

    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->providers as $type => $providers) {
            if ($event instanceof $type) {
                /** @var ListenerProviderInterface $provider */
                foreach ($providers as $provider) {
                    yield from $provider->getListenersForEvent($event);
                }
                return;
            }
        }

        yield from $this->defaultProvider->getListenersForEvent($event);
    }
}
