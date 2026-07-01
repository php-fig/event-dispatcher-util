# PSR-14 Event Dispatcher Utility Library

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]


This package contains a series of traits and base classes to cover the common, boilerplate portions of implementing a PSR-14-compliant event dispatcher library.


## Install

Via Composer

``` bash
$ composer require fig/event-dispatcher-util
```

## Usage

### Aggregate provider

The `AggregateProvider` class is a `ListenerProvider` implementation that collects and concatenates other listener providers.  It is a simple wrapper that `yield`s from a series of configured providers, in order.

```php
use Fig\EventDispatcher\AggregateProvider;

$provider1 = new YourProvider();
$provider2 = new SomeOtherProvider();

$provider = (new AggregateProvider())
    ->addProvider($provider1)
    ->addProvider($provider2)
;

$dispatcher = new SomeDispatcher($provider);
```

The aggregate provider will now return all the listeners from `YourProvider`, followed by all the listeners from `SomeOtherProvider`.

### Delegating provider

The `DelegatingProvider` is another multi-provider front-end.  In this case, it can be configured such that certain event types will use one sub-provider, and others will use a different one.  A given type can be configured with multiple sub-providers, and events will be matched using `instanceof` so that sub-classes of an event are also affected.

```php
use Fig\EventDispatcher\DelegatingProvider;

$requestProvider = new KernelListenerProvider();
$ormProvider = new OrmListenerProvider();

$defaultProvider = new SomeDefaultProvider();

// Assume HttpEvent and OrmEvent are interfaces for event classes.

$provider = (new DelegatingProvider($defaultProvider))
    ->addProvider($requestProvider, [HttpEvent::class])
    ->addProvider($ormProvider, [OrmEvent::class])
;

$dispatcher = new SomeDispatcher($provider);
```

Now, events sent to the dispatcher that implement `HttpEvent` will be passed along to `KernelListenerProvider` only, while those that implement `OrmEvent` will be passed along to `OrmListenerProvider` only.  Any other events will be passed on to the default provider only.

`AggregateProvider` and `DelegateProvider` are fully compatible with each other, so either can use an instance of the other as one of its sub-providers.

### Parameter provider helpers

The `ParameterDeriverTrait` is a set of tools to help listener provider implementations with deriving the type of a callable's parameter, to know what type of event it is.  PHP supports a wide variety of callable types, which are not always easy to disambiguate.  The tools in this class help ease that process.

If you are not writing your own Provider registration mechanism, this trait will not be useful.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email one of the current PHP-FIG Secretaries instead of using the issue tracker.

## License

This package is released under the MIT license. Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/fig/event-dispatcher-util.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/License-MIT-green.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/fig/event-dispatcher-util.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/fig/event-dispatcher-util
[link-downloads]: https://packagist.org/packages/fig/event-dispatcher-util
