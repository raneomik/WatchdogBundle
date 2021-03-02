WatchdogBundle
==============

A widget bundle providing a watchdog watching over time and which "woofs" if current time matches provided configuration.


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
    dates: # raise an event if current date time matches
    - { date_time: '2019-12-01 11:00' }           # a date and time
    - { date: '2019-12-03' }                      # a specific date
    - { hour: '14:00' }                           # a specific hour            
    - { time: '14:15' }                           # or time      
    - { start: '2019-12-01', end: '2019-12-31' }  # an interval of dates
    - { start: '10:00', end: '11:00' }            # or of times      
    - { relative: 'first wednesday of' }          # a relative format*
    - compound:                                   # or a composite of the rules above following "And" logic, for example :
      - { relative: 'monday' }                    # "On mondays"
      - { start: '20:00', end: '22:30' }          # "and between 20:00 and 22:30"
```

\* [relative format](https://www.php.net/manual/fr/datetime.formats.relative.php)


### Watchdog service

This bundle provides the `Raneomik\WatchdogBundle\Watchdog\Watchdog` service that can be injected anywhere.
It has a `isWoofTime` method returning true once configuration matches current date time.

### WatchdogHandlerInterface and WatchdogWoofCheckEvent

This bundle also provides the `Raneomik\WatchdogBundle\Handler\WatchdogHandlerInterface` interface with `processWoof(array $parameters = [])` method, for more complex processes.
It works with the `WatchdogSubscriber` subscribed to `WatchdogWoofCheckEvent`.

Anywhere you wish to check if it `isWoofTime`, dispatch a `new WatchdogWoofCheckEvent($parameters)`, 
and if it `isWoofTime`, it will trigger all `WatchdogHandlerInterface`s 
and their `processWoof(array $parameters = []) // passed to the WatchdogWoofCheckEvent constructor`.

If you wish to use it but your project isn't set to be autoconfigured, all your `Handlers` implementing `WatchdogHandlerInterface` must be tagged with `raneomik_watchdog.handler`.

#### TODO:

- [ ] more unit tests on watchdog units
- [ ] format restrictions on specific units (currently, you can pass any string which is supported by `\DateTime` to most of the config keys)
- [ ] maybe add Messenger component support
- [ ] other not seen yet
