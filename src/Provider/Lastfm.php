<?php

/**
 * Phergie plugin for returning the current or last played song for a user on last.fm or libre.fm
 *
 * @link https://github.com/chrismou/phergie-irc-plugin-react-audioscrobbler for the canonical source repository
 * @copyright Copyright (c) 2016 Chris Chrisostomou (https://mou.me)
 * @license http://phergie.org/license New BSD License
 * @package Chrismou\Phergie\Plugin\Audioscrobbler
 */

namespace Chrismou\Phergie\Plugin\Audioscrobbler\Provider;

use Phergie\Irc\Plugin\React\Command\CommandEvent as Event;

/**
 * Last.fm provider for the Audioscrobbler plugin for Phergie
 *
 * @category Chrismou
 * @package Chrismou\Phergie\Plugin\Audioscrobbler\Provider
 */
class Lastfm implements AudioscrobblerProviderInterface
{
    /**
     * @var string
     */
    protected $apiUrl = 'http://ws.audioscrobbler.com/2.0/';

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Validate the provided config
     *
     * @param array $config
     * @return boolean
     */
    public static function validateConfig($config)
    {
        // Requires an API key
        return ($config) ? true : false;
    }

    /**
     * Validate the provided parameters
     *
     * @param array $params
     * @return boolean
     */
    public function validateParams(array $params)
    {
        return (count($params) === 1) ? true : false;
    }

    /**
     * Get the url for the API request
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @return string
     */
    public function getApiRequestUrl(Event $event)
    {
        return sprintf("%s?%s", $this->apiUrl, http_build_query($this->getApiRequestParams($event)));
    }

    /**
     * Returns a querystring parameters array
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @return array
     */
    protected function getApiRequestParams(Event $event)
    {
        $params = $event->getCustomParams();
        $user = $params[0];

        return array(
            'format' => 'json',
            'api_key' => $this->apiKey,
            'method' => 'user.getrecenttracks',
            'user' => $user,
            'limit' => '1'
        );
    }

    /**
     * Returns an array of lines to send back to IRC when the http request is successful
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param string $apiResponse
     * @return array
     */
    public function getSuccessLines(Event $event, $apiResponse)
    {
        $response = json_decode($apiResponse);
        if (isset($response->recenttracks)) {
            $messages = array($this->getSuccessMessage($response));
        } else {
            $messages = $this->getNoResultsLines($event);
        }

        return $messages;
    }

    /**
     * Returns a message generated from the api request to use in the final response
     *
     * @param object $response
     * @return string
     */
    protected function getSuccessMessage($response)
    {
        $track = (is_array($response->recenttracks->track))
            ? $response->recenttracks->track[0]
            : $response->recenttracks->track;

        return sprintf(
            "%s %s listening to %s by %s %s[ %s ]",
            $response->recenttracks->{'@attr'}->user,
            (isset($track->{'@attr'}->nowplaying)) ? "is" : "was",
            $track->name,
            $track->artist->{'#text'},
            (!isset($track->{'@attr'}->nowplaying)) ? age($track->date->uts) . "ago " : "",
            $track->url
        );
    }

    /**
     * Returns an array of lines to send back to IRC when there are no results
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @return array
     */
    public function getNoResultsLines(Event $event)
    {
        return array(sprintf('%s does not exist on last.fm', $event->getCustomParams()[0]));
    }

    /**
     * Returns an array of lines for the help response
     *
     * @return array
     */
    public function getHelpLines()
    {
        return array(
            'Usage: lastfm [username]',
            '[username] - the last.fm user to look up',
            'Instructs the bot to query last.fm for this user\'s most recent listened track'
        );
    }
}
