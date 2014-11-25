Events
======

For the sake of plugins and general extensibility, __PHP_DDNS__ is going to need some sort of events system to allow core functionality to be altered without actually modifying the files.

## Structure ##

As yet I am unsure of where it should exist, or exactly what form it should take. My current ideas are:

* baked-in to `\PHP_DDNS\Core\PHP_DDNS`
* as `\PHP_DDNS\Core\PHP_DDNS_Events`
* the first of the official plugins
* a separate project which I make use of in __PHP_DDDNS__

## Usage ##

Despite being unsure of where to classify its existence, I already have a basic outline of how it will work. I will likely use [Symfony's Event/EventDispatcher](http://symfony.com/doc/current/components/event_dispatcher/introduction.html) system as inspiration.

### Overview ###

Events will consist of two entry points: *wishes* and *contracts* (names TBC). As these (temporary) names suggest one is more to do with ideas and the non-physical, while the other deals with fixed and certain things.

*Wishes* will be how plugins and the like can request to interfere with things like data manipulation, request handling, etc. Things that don't have a concrete existence as far as the input and final output are concerned, which most users aren't concerned with. Things that happen behind-the-scenes.

*Contracts* are how developers can interact with the user, by creating pages in the admin portal for their plugin's settings or adding content to an existing page if their plugin has a connection to existing functionality. These are things that are obvious to users, not limited to admin portal pages.

### States ###

The majority of *contracts* will likely have two states: `before` and `after`.

*Wishes*, on the other hand, may have many states. For example, a database `SELECT` may have the following states: `before`, `prepared`, `executed`, `success`, `error`, `after`.

### Integration ###

Once developed, there will be a way for developers to attach and emit both. `\PHP_DDNS\Core` will emit both at certain places, providing the infrastructure for plugin development. Even the planned official plugins like Lockdown and the admin portal will be made in the way I expect (hope?) all plugins will adhere to, by means of *wishes* and *contracts* rather than direct modification of core functionality.

Events will be in the form `eventName:stateName`, although developers will also be able to attach onto entire events rather than declaring an attach for each state separately.

I plan on having the same function for both emitting and attaching events. Something like this:

```php
// emit
\Events::wish( "updateDevice:success", array( $id, $device ) );
\Events::contract( "login:before", array( $template ) );

// attach
\Events::wish( "updateDevice:failure", "Announce@pushSingle", array( 'uuid' => 'c13bfd49d74bc4a291deddfbd8d30b8ac20b2527' ) );
\Events::contract( "login:before", "MyPlugin@outputLogo" );
```

## Documentation ##

Once I've finalised and implemented the events system, documentation of `\PHP_DDNS\Core` will include details of the events emitted and the parameters you should expect when attaching.

That said, I have no idea when I'll get around to such detailed documentation - I'll probably look for a way to add it to phpDoc's DocBlocks seeing as I'm already writing those as I go.