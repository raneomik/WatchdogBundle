WatchdogBundle
==============

|[![Workflow](https://github.com/raneomik/WatchdogBundle/actions/workflows/workflow.yaml/badge.svg)](https://github.com/raneomik/WatchdogBundle/actions/workflows/workflow.yaml)|[![codecov](https://codecov.io/gh/raneomik/WatchdogBundle/branch/main/graph/badge.svg?token=CAJ62EG1GB)](https://codecov.io/gh/raneomik/WatchdogBundle)|[![SymfonyInsight](https://insight.symfony.com/projects/2fc0de74-a97a-44df-ad48-c5534a2e8065/mini.svg)](https://insight.symfony.com/projects/2fc0de74-a97a-44df-ad48-c5534a2e8065)|[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Franeomik%2FWatchdogBundle%2Fmain)](https://dashboard.stryker-mutator.io/reports/github.com/raneomik/WatchdogBundle/main)|[![Type coverage](https://shepherd.dev/github/raneomik/WatchdogBundle/coverage.svg)](https://shepherd.dev/github/raneomik/WatchdogBundle)|[![psalm](https://shepherd.dev/github/raneomik/WatchdogBundle/level.svg)](https://shepherd.dev/github/raneomik/WatchdogBundle)|[![phpstan](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)](https://shepherd.dev/github/raneomik/WatchdogBundle)|
|:---:|:---:|:---:|:---:|:---:|:---:|:---:|


A widget bundle providing a watchdog which "woofs" if current time matches provided configuration.


### Installation

    composer require raneomik/watchdog-bundle

if not using symfony/flex, you need de register the bundle in your project :

```php
// config/bundles.php

return [
    Raneomik\WatchdogBundle\WatchdogBundle::class => ['all' => true],
];

```

### Configuration

It can be configured as follows:

 ```yaml
#config/packages/watchdog.yml

watchdog:
    default: # raise an event if current date time matches (following "Or" logic)
    - date_time: '2019-12-01 11:00'           # a date and time
    - date: '2019-12-03'                      # a specific date
    - hour: '14:00'                           # a specific hour            
    - time: '14:15'                           # or time      
    - { start: '2019-12-01', end: '2019-12-31' }  # an interval of dates
    - { start: '10:00', end: '11:00' }        # or of times      
    - relative: 'first wednesday of'          # a relative format*
    - compound:                               # or a composite of the rules above, following "And" logic, for example :
      - relative: 'monday'                    # "On mondays"
      - { start: '20:00', end: '22:30' }      # "and between 20:00 and 22:30"
    
    maintenance: # define another watchdogs, independent of each other.
    - compound:
      - relative: 'sundays' 
      - { start: '05:00', end: '06:00' }
```

\* [relative format](https://www.php.net/manual/datetime.formats.relative.php)


### Watchdog service

This bundle provides the `Raneomik\WatchdogBundle\Watchdog\WatchdogInterface` service that can be injected anywhere.
It has a `isWoofTime` method returning true once configuration matches current date time.
If several ones are defined, they can be injected as follows (based on the example above) :

```php
use Raneomik\WatchdogBundle\Watchdog\WatchdogInterface;

class Service
{
    __construct(
        private WatchdogInterface $default,
        private WatchdogInterface $maintenance
    ) {
    }
....
```

### WatchdogHandlerInterface and WatchdogWoofCheckEvent

This bundle also provides the `Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface` interface with `processWoof(array $parameters = [])` method, for more complex processes.
It works with the `WatchdogEventSubscriber` subscribed to `WatchdogWoofCheckEvent`.

Anywhere you wish to check if it `isWoofTime`, dispatch a `new WatchdogWoofCheckEvent($parameters, $watchdogId)`, 
and if it `isWoofTime`, it will trigger all `WatchdogHandlerInterface`s 
and their `processWoof(array $parameters = []) // passed to the WatchdogWoofCheckEvent constructor`.

`$watchdogId` is null by default, which results in all defined watchdogs check.
If one is passed by this parameter, it checks only the concerned one.

If you wish to use it but your project isn't set to be autoconfigured, all your `Handlers` implementing `WatchdogHandlerInterface` must be tagged with `raneomik_watchdog.handler`.

### *WIP*: Profiler data collection 

You can access watchdog data in Symfony profiler :

![profiler.png](doc/images/profiler.gif)

[License](LICENCE)
