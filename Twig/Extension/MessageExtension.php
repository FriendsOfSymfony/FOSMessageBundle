<?php

namespace Ornicar\MessageBundle\Twig\Extension;

use Ornicar\MessageBundle\Security\ParticipantProviderInterface;
use Ornicar\MessageBundle\Model\ReadableInterface;
use Ornicar\MessageBundle\Provider\ProviderInterface;

class MessageExtension extends \Twig_Extension
{
    protected $participantProvider;
    protected $provider;

    protected $nbUnreadMessagesCache;

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
            'ornicar_message_is_read'  => new \Twig_Function_Method($this, 'isRead'),
            'ornicar_message_nb_unread' => new \Twig_Function_Method($this, 'getNbUnread')
        );
    }

    /**
     * Tells if this readable (thread or messae) is read by the current user
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
        return 'ornicar_message';
    }
}
