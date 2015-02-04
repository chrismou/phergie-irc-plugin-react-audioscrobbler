<?php

/**
 * Phergie plugin for returning the current or last played song for a user on last.fm or libre.fm
 *
 * @link https://github.com/chrismou/phergie-irc-plugin-react-audioscrobbler for the canonical source repository
 * @copyright Copyright (c) 2014 Chris Chrisostomou (http://mou.me)
 * @license http://phergie.org/license New BSD License
 * @package Chrismou\Phergie\Plugin\Audioscrobbler
 */

namespace Chrismou\Phergie\Plugin\Audioscrobbler\Provider;

use Phergie\Irc\Plugin\React\Command\CommandEvent;

/**
 * Audioscrobbler plugin class
 *
 * @category Chrismou
 * @package Chrismou\Phergie\Plugin\Audioscrobbler\Provider
 */
interface AudioscrobblerProviderInterface
{
    /**
     * Validate the provided config
     *
     * @param array $config
     *
     * @return boolean
     */
    public static function validateConfig($config);

    /**
     * Return the url for the API request
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     *
     * @return string
     */
    public function getApiRequestUrl(CommandEvent $event);

    /**
     * Validate the provided parameters
     *
     * @param array $params
     *
     * @return boolean
     */
    public function validateParams(array $params);

    /**
     * Returns an array of lines to send back to IRC when the http request is successful
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param string $apiResponse
     *
     * @return array
     */
    public function getSuccessLines(CommandEvent $event, $apiResponse);

    /**
     * Returns an array of lines for the help response
     *
     * @return array
     */
    public function getHelpLines();
}
