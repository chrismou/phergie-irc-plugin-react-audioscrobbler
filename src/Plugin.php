<?php
/**
 * Phergie plugin for returning the current or last played song for a user on last.fm (https://github.com/chrismou/phergie-irc-plugin-react-audioscrobbler)
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
use Chrismou\Phergie\Plugin\Audioscrobbler\Provider as Provider;


/**
 * Plugin class.
 *
 * @category Chrismou
 * @package Chrismou\Phergie\Plugin\Audioscrobbler
 */
class Plugin extends AbstractPlugin
{
    /**
     * @var Provider\AudioscrobblerProviderInterface
     */
    protected $lastfmProvider;

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
        $this->lastfmProvider = (isset($config['lastfmApiKey'])) ? new Provider\Lastfm($config['lastfmApiKey']) : false;
    }

    /**
     *
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'command.lastfm' => 'handleLastfmCommand'
        );
    }

    /**
     * Handle the lastfm command
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     */
    public function handleLastfmCommand(Event $event, Queue $queue)
    {
        if (!$this->lastfmProvider) $this->logger->debug('[audioscrobbler] No Provider');
        else $this->handleCommand($event, $queue, $this->lastfmProvider);


    }

    public function handleCommand(Event $event, Queue $queue, Provider\AudioscrobblerProviderInterface $provider) {
        if ($provider->validateParams($event->getCustomParams())) {
            $request = $this->getApiRequest($event, $queue, $provider);
            $this->getEventEmitter()->emit('http.request', array($request));
        } else {
            $this->handleCommandhelp($event, $queue, $provider);
        }
    }

    /**
     * Handler for the weather help command
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     */
    public function handleCommandHelp(Event $event, Queue $queue, Provider\AudioscrobblerProviderInterface $provider) {
        $this->sendIrcResponse($event, $queue, $provider->getHelpLines());
    }

    /**
     * Set up the API request and set the callbacks
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     *
     * @return \WyriHaximus\Phergie\Plugin\Http\Request
     */
    protected function getApiRequest(Event $event, Queue $queue, Provider\AudioscrobblerProviderInterface $provider)
    {
        $self = $this;
        return new HttpRequest(array(
            'url' => $provider->getApiRequestUrl($event, $queue),
            'resolveCallback' => function ($data) use ($self, $event, $queue, $provider) {
                $self->sendIrcResponse($event, $queue, $provider->getSuccessLines($event, $data));
            },
            'rejectCallback' => function ($error) use ($self, $event, $queue, $provider) {
                $self->sendIrcResponse($event, $queue, $self->getRejectLines($event, $error));
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
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param string $apiError
     *
     * @return array
     */
    public function getRejectLines(Event $event, $apiError)
    {
        return array('Something went wrong... ಠ_ಠ');
    }

    /**
     * Return an array of lines to send back to IRC when the request fails
     *
     * @param \Phergie\Irc\Plugin\React\Command\CommandEvent $event
     * @param string $apiError
     *
     * @return array
     */
    public function getProvider($prefix)
    {
        return $this->{$prefix."Provider"};
    }
}
