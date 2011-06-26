<?php

namespace Ornicar\MessageBundle\Reader;

use Ornicar\MessageBundle\Authorizer\AuthorizerInterface;
use Ornicar\MessageBundle\Model\ReadableInterface;

class Reader implements ReaderInterface
{
    /**
     * The authorizer instance
     *
     * @var AuthorizerInterface
     */
    protected $authorizer;

    public function __construct(AuthorizerInterface $authorizer)
    {
        $this->authorizer = $authorizer;
    }

    /**
     * Marks the readable as read by the current authenticated user
     *
     * @param ReadableInterface $readable
     */
    public function markAsRead(ReadableInterface $readable)
    {
        $readable->setIsReadByParticipant($this->getAuthenticatedUser(), true);
    }

    /**
     * Marks the readable as unread by the current authenticated user
     *
     * @param ReadableInterface $readable
     */
    public function markAsUnread(ReadableInterface $readable)
    {
        $readable->setIsReadByParticipant($this->getAuthenticatedUser(), false);
    }

    /**
     * Gets the current authenticated user
     *
     * @return UserInterface
     */
    protected function getAuthenticatedUser()
    {
        return $this->authorizer->getAuthenticatedUser();
    }
}
