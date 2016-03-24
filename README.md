# Last.fm / Libre.fm plugin for [Phergie](http://github.com/phergie/phergie-irc-bot-react/)

[Phergie](http://github.com/phergie/phergie-irc-bot-react/) plugin for returning the current or last played song for a user on last.fm or libre.fm.

[![Build Status](https://scrutinizer-ci.com/g/chrismou/phergie-irc-plugin-react-audioscrobbler/badges/build.png?b=master)](https://scrutinizer-ci.com/g/chrismou/phergie-irc-plugin-react-audioscrobbler/build-status/master)
[![Test Coverage](https://codeclimate.com/github/chrismou/phergie-irc-plugin-react-audioscrobbler/badges/coverage.svg)](https://codeclimate.com/github/chrismou/phergie-irc-plugin-react-audioscrobbler/coverage)
[![Code Climate](https://codeclimate.com/github/chrismou/phergie-irc-plugin-react-audioscrobbler/badges/gpa.svg)](https://codeclimate.com/github/chrismou/phergie-irc-plugin-react-audioscrobbler)
[![Buy me a beer](https://img.shields.io/badge/donate-PayPal-019CDE.svg)](https://www.paypal.me/chrismou)

## About
[Phergie](http://github.com/phergie/phergie-irc-bot-react/) plugin for returning the current or last played song for a user on 
last.fm or libre.fm.  By default, the plugin responds to the commands "lastfm username" and "librefm username" (without the quotes).

## Install

The recommended method of installation is [through composer](http://getcomposer.org).

```JSON
composer require chrismou/phergie-irc-plugin-react-audioscrobbler
```

See Phergie documentation for more information on
[installing and enabling plugins](https://github.com/phergie/phergie-irc-bot-react/wiki/Usage#plugins).

## Configuration

For last.fm lookups, you need a free API key which you can get from [here](http://www.last.fm/api). 
LibreFM works out of the box.

```php
new \Chrismou\Phergie\Plugin\Audioscrobbler\Plugin(array(
    'lastfm' => 'YOUR_API_KEY'
))
```

## CommandHelp compatibility

The plugin activates lastfm when it sees you've included a lastfm config, so in order to get [CommandHelp](http://github.com/phergie/phergie-irc-plugin-react-commandhelp/) 
to show the plugin in it's command list , you'll need to pass a mock value:

```php
new \Phergie\Irc\Plugin\React\CommandHelp\Plugin(array(
    'plugins' => array(
        new \Chrismou\Phergie\Plugin\Audioscrobbler\Plugin(array(
            'lastfm' => true
        )),
    )
))
```

Adding this line to your CommandHelp config should force the lastfm command to be displayed.

## Tests

To run the unit test suite:

```
curl -s https://getcomposer.org/installer | php
php composer.phar install
./vendor/bin/phpunit
```

If you use docker, you can also run the test suite against all supported PHP versions:
```
./vendor/bin/dunit
```

## License

Released under the BSD License. See `LICENSE`.
