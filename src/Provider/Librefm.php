<?php

/**
 * Phergie plugin for returning the current or last played song for a user on last.fm or libre.fm (https://github.com/chrismou/phergie-irc-plugin-react-audioscrobbler)
 *
 * @link https://github.com/chrismou/phergie-irc-plugin-react-audioscrobbler for the canonical source repository
 * @copyright Copyright (c) 2014 Chris Chrisostomou (http://mou.me)
 * @license http://phergie.org/license New BSD License
 * @package Chrismou\Phergie\Plugin\Audioscrobbler
 */

namespace Chrismou\Phergie\Plugin\Audioscrobbler\Provider;

use Phergie\Irc\Plugin\React\Command\CommandEvent as Event;

/**
 * Libre.fm provider for the Audioscrobbler plugin for Phergie
 *
 * @category Chrismou
 * @package Chrismou\Phergie\Plugin\Audioscrobbler\Provider
 */
class Librefm extends Lastfm
{
    /**
     * @var string
     */
    protected $apiUrl = 'https://libre.fm/2.0/';

    /**
     * @param string $config
     */
    public function __construct($config)
    {

    }

    /**
     * Validate the provided config
     *
     * @param array $config
     * @return true|false
     */
    public static function validateConfig($config)
    {
        // No API key required
        return true;
    }

    /**
     * Get the url for the API request
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @return string
     */
    public function getApiRequestUrl(Event $event)
    {
        $params = $this->getApiRequestParams($event);
        unset($params['api_key']);

        return sprintf("%s?%s", $this->apiUrl, http_build_query($params));
    }

    /**
     * Returns an array of lines to send back to IRC when there are no results
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param string $apiResponse
     * @return array
     */
    public function getNoResultsLines(Event $event, $apiResponse)
    {
        return array('This user does not exist on libre.fm');
    }

    /**
     * Returns an array of lines for the help response
     * @return array
     */
    public function getHelpLines()
    {
        return array(
            'Usage: librefm [username]',
            '[username] - the last.fm user to look up',
            'Instructs the bot to query libre.fm for this user\'s most recent listened track'
        );
    }
}
