PHP_DDNS
========

__PHP_DDNS__ is designed to add, update and remove device IP addresses from a database, allowing you to keep track of your devices for providing a way to access them remotely - all from your existing hosting + domain combo.

In short: the goal is to make a simple, self-hosted and personal Dynamic DNS tool for just about anyone with a hosting package (PHP and MySQL a must) and domain.

Made in response to discovering [Remonit](https://github.com/zefei/remonit), to hopefully make it easier for use on my PC/laptop. Other ideas I've had since starting are a push-notifications service and an IP-restrictive authorisation tool - but if I have a go at making these myself, __PHP_DDNS__ will be the core and they will be made as separate projects, possibly even plugins.

## VERSION 1 RELEASED ##

__PHP_DDNS__ has now reached v1.0, which means all core functionality is implemented! Hurray!

Keep an eye on the [to-do list](#to-do) below to see the currently implemented features, as well as the estimated order of yet-to-be-done features.

## How It Works ##

Once everything is installed the `pinger` script on your devices will periodically call a `hook` on your domain, updating their entry in the database.

You can add and remove devices programmatically or via the admin portal, once developed, giving you plenty of control over the devices being tracked. Devices update themselves, but can only be added or removed from the portal/programmatically.

Using the public methods of __PHP_DDNS__ you can easily retrieve the details of a tracked device, allowing you to build interfaces/tunnels/whatever for services running on it.

## Requirements ##

Do not consider this list complete, it is an estimate based on current development progress:

1. __Server__
  * PHP >= 5.3.0
  * cURL module
  * PDO + MySQL driver
  * MySQL (version TBD)
  * Write access
  * Domain or fixed IP
2. __Device__
  * PHP >= 5.3.0
  * cURL module
  * Script located somewhere it can write a file

## Installation ##

### Server ###

1. Copy the contents of the `server` directory to your hosting.
2. Hit the included `install.php` via a web browser - this will set up the database.
3. Now add devices!

### Devices ###

1. Put the contents of the `device` directory somewhere on your device.
  * __Note:__ make sure you put it somewhere it can write files!
3. Run `php pinger.php "setup" "<hook URL>"` on the device you want to add.
4. Put `pinger`'s output through `PHP_DDNS->addDevice()` on your server.
5. Setup a recurring task on your device to run `php pinger.php` automatically.
  * I recommend pinging at least once per day, laptops should be even more frequent.
6. Repeat steps 3 through 5 for all the devices you want to track.
7. That's it! Your devices should now be tracked by __PHP_DDNS__!

## To Do ##

### Main ###

The section numbers in this list should roughly equate to major version releases, once all sub-tasks are crossed out. The tasks are (hopefully) in order of importance, and (almost definitely) in order of when I'll do them.

1. ~~Background Stuff~~
  * ~~Database Helper~~
  * ~~__PHP_DDNS__ class outline~~
  * ~~Flesh out predictable core support functions~~
  * ~~Attempt one of add/update/remove to develop system logic~~
2. ~~Initial Release~~
  * ~~Refactor code to move from "machines" to "devices" terminology.~~
  * ~~Update to use namespacing.~~
  * ~~Implement adding devices.~~
  * ~~Implement removing devices.~~
  * ~~Write the `hook`.~~
  * ~~Write the `pinger`.~~
  * ~~Write the server-side installer.~~
3. Second Release
  * Tidy `hook` and `pinger`.
  * Refactor with emphasis on plugins/extensibility
  * Flesh out Requirements, tweak Installation.
  * Switch to Bootstrap + [Material](https://github.com/FezVrasta/bootstrap-material-design) [Design](https://github.com/ebidel/material-playground/) for HTML output.
  * Build the admin portal.
  * See if I can do an empty `CREATE TABLE` followed by an `ALTER TABLE`
    * Hopefully this way will be more upgrade-friendly
  * Implement specifying of ports for devices.
  * More detailed Usage instructions.
4. Future Features
  * Constant connections/streaming/sockets/etc
  * Write a wiki
  * Allow device name to be changed.
  * Translate `pinger` into languages besides PHP.

### Plugins ###

This is a rough list of the "official" plugins for __PHP_DDNS__ that I will be developing and maintaining alongside the core, in no meaningful order.

1. "simple auth" (name TBC) - lets you password-protect the entry point for initiating connections to your devices.
2. "admin portal" (name TBC) - provides a web interface for adding/removing devices, rather than programmatically.
3. "Lockdown" - a system to easily deny access to parts of your site except from tracked devices.
4. "Announce" - push notifications to one or more tracked devices.

## Credits ##

* [Remonit](https://github.com/zefei/remonit) for giving me the inspiration for this.
* [Crypt](https://github.com/Hunter-Dolan/Crypt) for giving me a simple two-way encryption system.
* [GitHub](https://github.com/) for just generally being awesome

## License ##

As usual with my work, this project is available under the BSD 3-Clause license. In short, you can do whatever you want with this code as long as:

* I am always recognised as the original author.
* I am not used to advertise any derivative works without prior permission.
* You include a copy of said license and attribution with any and all redistributions of this code, including derivative works.

For more details, read the included LICENSE.md file or read about it on [opensource.org](http://opensource.org/licenses/BSD-3-Clause).
