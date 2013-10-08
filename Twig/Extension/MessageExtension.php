<?php

namespace FOS\MessageBundle\Twig\Extension;

use FOS\MessageBundle\Security\ParticipantProviderInterface;
use FOS\MessageBundle\Model\ReadableInterface;
use FOS\MessageBundle\Provider\ProviderInterface;

class MessageExtension extends \Twig_Extension
{
    protected $participantProvider;
    protected $provider;

    protected $nbUnreadMessagesCache;
    protected $nbUnreadThreadsCache;

    public function __construct(ParticipantProviderInterface $participantProvider, ProviderInterface $provider)
    {
        $this->participantProvider = $participantProvider;
        $this->provider = $provider;
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getFunctions()
    {
        return array(
            'fos_message_is_read'  => new \Twig_Function_Method($this, 'isRead'),
            'fos_message_nb_unread' => new \Twig_Function_Method($this, 'getNbUnread'),
            'fos_message_nb_unread_threads' => new \Twig_Function_Method($this, 'getNbUnreadThreads')
        );
    }

    /**
     * Tells if this readable (thread or message) is read by the current user
     *
     * @return boolean
     */
    public function isRead(ReadableInterface $readable)
    {
        return $readable->isReadByParticipant($this->getAuthenticatedParticipant());
    }

    /**
     * Gets the number of unread messages for the current user
     *
     * @return int
     */
    public function getNbUnread()
    {
        if (null === $this->nbUnreadMessagesCache) {
            $this->nbUnreadMessagesCache = $this->provider->getNbUnreadMessages();
        }

        return $this->nbUnreadMessagesCache;
    }

    /**
     * Get the number of unread threads for the current user
     * @return int
     */
    public function getNbUnreadThreads()
    {
        if (null === $this->nbUnreadThreadsCache) {
            $this->nbUnreadThreadsCache = $this->provider->getNbUnreadThreads();
        }

        return $this->nbUnreadThreadsCache;
    }

    /**
     * Gets the current authenticated user
     *
     * @return ParticipantInterface
     */
    protected function getAuthenticatedParticipant()
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'fos_message';
    }
}
