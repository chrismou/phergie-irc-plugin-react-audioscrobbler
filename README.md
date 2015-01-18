# Last.fm / Libre.fm plugin for [Phergie](http://github.com/phergie/phergie-irc-bot-react/)

[Phergie](http://github.com/phergie/phergie-irc-bot-react/) plugin for returning the current or last played song for a user on last.fm or libre.fm.

[![Build Status](https://scrutinizer-ci.com/g/chrismou/phergie-irc-plugin-react-audioscrobbler/badges/build.png?b=master)](https://scrutinizer-ci.com/g/chrismou/phergie-irc-plugin-react-audioscrobbler/build-status/master)


## About
[Phergie](http://github.com/phergie/phergie-irc-bot-react/) plugin for returning the current or last played song for a user on 
last.fm or libre.fm.  By default, the plugin responds to the commands "lastfm username" and "librefm username" (without the quotes).

## Install

The recommended method of installation is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "chrismou/phergie-irc-plugin-react-audioscrobbler": "~1"
    }
}
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

## Tests

To run the unit test suite:

```
curl -s https://getcomposer.org/installer | php
php composer.phar install
./vendor/bin/phpunit
```

## License

Released under the BSD License. See `LICENSE`.
