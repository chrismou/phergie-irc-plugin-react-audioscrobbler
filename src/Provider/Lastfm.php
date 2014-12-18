<?php

namespace Chrismou\Phergie\Plugin\Audioscrobbler\Provider;

use Phergie\Irc\Plugin\React\Command\CommandEvent as Event;

class Lastfm implements AudioscrobblerProviderInterface
{
    /**
     * @var string
     */
    protected $apiUrl = 'http://ws.audioscrobbler.com/2.0/';

    /**
     * @var string
     */
    protected $apiKey = '';

    /**
     * @param string $apiKey
     */
    function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * Validate the provided parameters
     *
     * @param array $params
     * @return true|false
     */
    public static function validateConfig($config)
    {
        return true;
    }

    /**
     * Validate the provided parameters
     *
     * @param array $params
     * @return true|false
     */
    public function validateParams(array $params)
    {
        return (count($params)==1) ? true : false;
    }

    /**
     * Get the url for the API request
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     * @return string
     */
    public function getApiRequestUrl(Event $event)
    {
        $params = $event->getCustomParams();
        $user = $params[0];

        $querystringParams = array(
            'format' => 'json',
            'api_key' => $this->apiKey,
            'method' => 'user.getrecenttracks',
            'user' => $user,
            'limit' => '1'
        );

        return sprintf("%s?%s", $this->apiUrl, http_build_query($querystringParams));
    }

    /**
     * Returns an array of lines to send back to IRC when the http request is successful
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param string $apiResponse
     *
     * @return array
     */
    public function getSuccessLines(Event $event, $apiResponse)
    {
        //var_dump($this->getApiRequestUrl($event));
        $response = json_decode($apiResponse);
        if (isset($response->recenttracks)) { // results?
            $track = (is_array($response->recenttracks->track)) ? $response->recenttracks->track[0] : $response->recenttracks->track;
            $messages = array(sprintf(
                "%s %s listening to %s by %s [ %s ]",
                $response->recenttracks->{'@attr'}->user,
                (isset($track->{'@attr'}->nowplaying)) ? "is" : "was",
                $track->name,
                $track->artist->{'#text'},
                $track->url
            ));
        } else {
            $messages = $this->getNoResultsLines($event, $apiResponse);
        }

        return $messages;
    }

    /**
     * Returns an array of lines to send back to IRC when there are no results
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param string $apiResponse
     *
     * @return array
     */
    public function getNoResultsLines(Event $event, $apiResponse)
    {
        return array('This user does not exist on last.fm');
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