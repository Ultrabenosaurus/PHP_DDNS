PHP_DDNS
========

__PHP_DDNS__ is designed to add, update and remove device IP addresses from a database, allowing you to keep track of your devices for providing a way to access them remotely - all from your existing hosting + domain combo.

In short: the goal is to make a simple, self-hosted and personal Dynamic DNS tool for just about anyone.

Made in response to discovering [Remonit](https://github.com/zefei/remonit), to hopefully make it easier for use on my PC/laptop.

## NOT YET USABLE ##

PHP_DDNS has not yet reached v1.0, which means not all core functionality is implemented.

Keep an eye on section *2. Initial Release* of the [to-do list](#to-do) below.

## How It Works ##

Once everything is installed the pinger script on your devices will periodically call a hook on your domain, updating their entry in the database.

You can add and remove devices programmatically or via the admin portal, once developed, giving you plenty of control over the devices being tracked. Devices update themselves, but can only be added or removed from the portal/programmatically.

Using the public methods of __PHP_DDNS__ you can easily retrieve the details of a tracked device, allowing you to build interfaces/tunnels/whatever for services running on it.

## Installation ##

### Requirements ###

TBD

### Server ###

1. Copy the contents of the `server` directory to your hosting.
2. Hit the included `index.php` via a web browser - this will set up the database.
3. Log in to the admin portal using the default credentials and change them immediately.

### Devices ###

1. Put the contents of the `devices` directory somewhere on your device.
  * __Note:__ make sure your put it somewhere it can write files!
2. Edit `config.php` to point to `hook.php` on your hosting.
3. Run `pinger.php "setup"` on the device you want to add.
4. Enter pinger's output into the Add Device page of the portal.
5. Setup a recurring task on your device to run `pinger.php` automatically
  * I recommend pinging at least once per day.
6. Repeat steps 4 through 6 for all the devices you want to track.
7. That's it! Your devices should now be tracked by __PHP_DDNS__

## To Do ##

The section numbers in this list should roughly equate to major version releases, once all sub-tasks are crossed out. The tasks are (hopefully) in order of importance, and (almost definitely) in order of when I'll do them.

0. ~~Background Stuff~~
  * ~~Database Helper~~
  * ~~__PHP_DDNS__ class outline~~
  * ~~Flesh out predictable core support functions~~
  * ~~Attempt one of add/update/remove to develop system logic~~
1. Initial Release
  * Refactor code to move from "machines" to "devices" terminology.
  * Implement adding devices.
  * Implement removing devices.
  * Write the hook.
  * Write the pinger.
2. Future Features
  * Flesh out Requirements, tweak Installation.
  * Build the admin portal.
  * Implement specifying of ports for devices.
  * More detailed usage instructions.
  * Translate hook into languages besides PHP.

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
