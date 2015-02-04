<?php

/**
 * Phergie plugin for returning the current or last played song for a user on last.fm or libre.fm
 *
 * @link https://github.com/chrismou/phergie-irc-plugin-react-audioscrobbler for the canonical source repository
 * @copyright Copyright (c) 2014 Chris Chrisostomou (http://mou.me)
 * @license http://phergie.org/license New BSD License
 * @package Chrismou\Phergie\Plugin\Audioscrobbler
 */

namespace Chrismou\Phergie\Plugin\Audioscrobbler;

use Phergie\Irc\Bot\React\AbstractPlugin;
use Phergie\Irc\Bot\React\EventQueueInterface as Queue;
use Phergie\Irc\Plugin\React\Command\CommandEvent as Event;
use WyriHaximus\Phergie\Plugin\Http\Request as HttpRequest;
use Chrismou\Phergie\Plugin\Audioscrobbler\Provider;

/**
 * Plugin class.
 *
 * @category Chrismou
 * @package Chrismou\Phergie\Plugin\Audioscrobbler
 */
class Plugin extends AbstractPlugin
{
    /**
     * Populated with valid providers at runtime
     * @var array
     */
    protected $validProviders = array();

    /**
     * All providers
     * @var array
     */
    protected $supportedProviders = array(
        "lastfm" => "Chrismou\\Phergie\\Plugin\\Audioscrobbler\\Provider\\Lastfm",
        "librefm" => "Chrismou\\Phergie\\Plugin\\Audioscrobbler\\Provider\\Librefm"
    );

    /**
     * Accepts plugin configuration.
     *
     * Supported keys:
     *
     *
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        foreach ($this->supportedProviders as $provider => $class) {
            $providerConfig = isset($config[$provider]) ? $config[$provider] : null;
            if ($class::validateConfig($providerConfig)) {
                $this->validProviders[$provider] = new $class($providerConfig);
            }
        }
    }

    /**
     * Return array of subscribed events
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        $subscribedEvents = array();
        foreach ($this->validProviders as $provider => $class) {
            $subscribedEvents['command.' . $provider] = 'handleCommand';
        }
        return $subscribedEvents;
    }

    /**
     * Handler for the main commands
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     */
    public function handleCommand(Event $event, Queue $queue)
    {
        $provider = $this->getProvider($event->getCustomCommand());
        if ($provider->validateParams($event->getCustomParams())) {
            $request = $this->getApiRequest($event, $queue);
            $this->getEventEmitter()->emit('http.request', array($request));
        } else {
            $this->handleCommandhelp($event, $queue);
        }
    }

    /**
     * Handler for the help command
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     */
    public function handleCommandHelp(Event $event, Queue $queue)
    {
        $provider = $this->getProvider($event->getCustomCommand());
        $this->sendIrcResponse($event, $queue, $provider->getHelpLines());
    }

    /**
     * Set up the API request and set the callbacks
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     * @return \WyriHaximus\Phergie\Plugin\Http\Request
     */
    protected function getApiRequest(Event $event, Queue $queue)
    {
        $provider = $this->getProvider($event->getCustomCommand());
        $self = $this;
        return new HttpRequest(array(
            'url' => $provider->getApiRequestUrl($event),
            'resolveCallback' => function ($data) use ($self, $event, $queue, $provider) {
                $self->sendIrcResponse($event, $queue, $provider->getSuccessLines($event, $data));
            },
            'rejectCallback' => function ($error) use ($self, $event, $queue, $provider) {
                $self->sendIrcResponse($event, $queue, $self->getRejectLines($error));
            }
        ));
    }

    /**
     * Send an array of response lines back to IRC
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     * @param array $ircResponse
     */
    protected function sendIrcResponse(Event $event, Queue $queue, array $ircResponse)
    {
        foreach ($ircResponse as $ircResponseLine) {
            $this->sendIrcResponseLine($event, $queue, $ircResponseLine);
        }
    }

    /**
     * Send a single response line back to IRC
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     * @param string $ircResponseLine
     */
    protected function sendIrcResponseLine(Event $event, Queue $queue, $ircResponseLine)
    {
        $queue->ircPrivmsg($event->getSource(), $ircResponseLine);
    }

    /**
     * Return an array of lines to send back to IRC when the request fails
     * @return array
     */
    public function getRejectLines($error)
    {
        return array('Something went wrong... ಠ_ಠ');
    }

    /**
     * Get a single provider class by command
     *
     * @param string $command
     * @return \Chrismou\Phergie\Plugin\Audioscrobbler\Provider\AudioscrobblerProviderInterface $provider|false
     */
    public function getProvider($command)
    {
        return (isset($this->validProviders[$command])) ? $this->validProviders[$command] : false;
    }
}
