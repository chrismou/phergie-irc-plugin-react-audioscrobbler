# Last.fm / Libre.fm plugin for [Phergie](http://github.com/phergie/phergie-irc-bot-react/)

[Phergie](http://github.com/phergie/phergie-irc-bot-react/) plugin for returning the current or last played song for a user on last.fm or libre.fm.

[![Build Status](https://travis-ci.org/chrismou/phergie-irc-plugin-react-audioscrobbler.svg?branch=master)](https://travis-ci.org/chrismou/phergie-irc-plugin-react-audioscrobbler)
[![Code Climate](https://codeclimate.com/github/chrismou/phergie-irc-plugin-react-audioscrobbler/badges/gpa.svg)](https://codeclimate.com/github/chrismou/phergie-irc-plugin-react-audioscrobbler)
[![Test Coverage](https://codeclimate.com/github/chrismou/phergie-irc-plugin-react-audioscrobbler/badges/coverage.svg)](https://codeclimate.com/github/chrismou/phergie-irc-plugin-react-audioscrobbler)

## Install

The recommended method of installation is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "chrismou/phergie-irc-plugin-react-audioscrobbler": "dev-master"
    }
}
```

See Phergie documentation for more information on
[installing and enabling plugins](https://github.com/phergie/phergie-irc-bot-react/wiki/Usage#plugins).

## Configuration

```php
new \Chrismou\Phergie\Plugin\Audioscrobbler\Plugin(array(



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
