WatchdogBundle
==============

A widget bundle providing a watchdog watching over time and which "woofs" if current time matches provided configuration.


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
    - { relative: 'first wednesday of' }          # a [relative format](https://www.php.net/manual/fr/datetime.formats.relative.php)
    - compound:                                   # or a composite of the rules above following "And" logic, for example :
      - { relative: 'monday' }                    # "On mondays"
      - { start: '20:00', end: '22:30' }          # "and between 20:00 and 22:30"
```

### Watchdog class

This bundle provides the `Raneomik\WatchdogBundle\Watchdog\Watchdog` class that can be injected anywhere.
It has a `isWoofTime` method returning true once configuration matches current date time.

### WatchdogHandlerInterface

This bundle provides the `Raneomik\WatchdogBundle\Watchdog\Watchdog` class that can be injected anywhere.
It has a `isWoofTime` method returning true once configuration matches current date time.

