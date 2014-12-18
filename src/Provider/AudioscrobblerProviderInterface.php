<?php

namespace Chrismou\Phergie\Plugin\Audioscrobbler\Provider;

use Phergie\Irc\Plugin\React\Command\CommandEvent;

interface AudioscrobblerProviderInterface
{
    /**
     * Validate the provided parameters
     *
     * @param array $params
     *
     * @return true|false
     */
    static function validateConfig($config);

    /**
     * Return the url for the API request
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     *
     * @return string
     */
    function getApiRequestUrl(CommandEvent $event);

    /**
     * Validate the provided parameters
     *
     * @param array $params
     *
     * @return true|false
     */
    function validateParams(array $params);

    /**
     * Returns an array of lines to send back to IRC when the http request is successful
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param string $apiResponse
     *
     * @return array
     */
    function getSuccessLines(CommandEvent $event, $apiResponse);

    /**
     * Returns an array of lines for the help response
     *
     * @return array
     */
    public function getHelpLines();
}