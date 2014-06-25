<?php
namespace FOS\MessageBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\MessageBundle\Event\FOSMessageEvents;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\EventDispatcher\Event;

/**
 * The flash listener adds flash messages when certain message events occur
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class FlashListener implements EventSubscriberInterface
{
    private static $successMessages = array(
        FOSMessageEvents::POST_SEND => 'flash_post_send_success',
        FOSMessageEvents::POST_DELETE => 'flash_thread_delete_success',
        FOSMessageEvents::POST_UNDELETE => 'flash_thread_undelete_success',
    );

    /**
     * Translator
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Session
     *
     * @var Session
     */
    protected $session;

    /**
     * The flash key
     *
     * @var string
     */
    protected $key;

    /**
     * Constructor.
     *
     * @param Session             $session    The current session
     * @param TranslatorInterface $translator A translator instance
     * @param string              $key        The flash key
     */
    public function __construct(Session $session, TranslatorInterface $translator, $key)
    {
        $this->session = $session;
        $this->translator = $translator;
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
         return array(
             FOSMessageEvents::POST_SEND => 'addSuccessFlash',
             FOSMessageEvents::POST_DELETE => 'addSuccessFlash',
             FOSMessageEvents::POST_UNDELETE => 'addSuccessFlash'
         );
    }

    /**
     * Adds a flashmessage to the session
     *
     * @param Event $event The current event
     *
     * @throws \InvalidArgumentException
     */
    public function addSuccessFlash(Event $event)
    {
        $eventName = $event->getname();

        if (!isset(self::$successMessages[$eventName])) {
            throw new \InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $this->session->getFlashBag()->add($this->key, $this->trans(self::$successMessages[$eventName]));
    }

    private function trans($message, array $params = array())
    {
        return $this->translator->trans($message, $params, 'FOSMessageBundle');
    }
}
