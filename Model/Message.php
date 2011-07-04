<?php

namespace Ornicar\MessageBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Ornicar\MessageBundle\Model\ParticipantInterface;
use DateTime;

/**
 * Abstract message model
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class Message implements MessageInterface
{
    /**
     * Unique id of the message
     *
     * @var mixed
     */
    protected $id;

    /**
     * User who sent the message
     *
     * @var ParticipantInterface
     */
    protected $sender;

    /**
     * Text body of the message
     *
     * @var string
     */
    protected $body;

    /**
     * Date when the message was sent
     *
     * @var DateTime
     */
    protected $createdAt;

    /**
     * Thread the message belongs to
     *
     * @var ThreadInterface
     */
    protected $thread;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Gets the message unique id
     *
     * @return mixed
     **/
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ThreadInterface
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * @param  ThreadInterface
     * @return null
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param  string
     * @return null
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return ParticipantInterface
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param  ParticipantInterface
     * @return null
     */
    public function setSender(ParticipantInterface $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Gets the created at timestamp
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->getCreatedAt()->getTimestamp();
    }
}
