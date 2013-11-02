<?php

namespace FOS\MessageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Thread the message belongs to
     *
     * @var ThreadInterface
     */
    protected $thread;

    /**
     * Collection of MessageMetadata
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $metadata;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->metadata = new ArrayCollection();
    }

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
     * @param ThreadInterface $thread
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }

    /**
     * @return \DateTime
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
     * @param string $body
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
     * @param ParticipantInterface $sender
     */
    public function setSender(ParticipantInterface $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Adds MessageMetadata to the metadata collection.
     *
     * @param MessageMetadata $meta
     */
    public function addMetadata(MessageMetadata $meta)
    {
        $this->metadata->add($meta);
    }

    /**
     * Get the collection of MessageMetadata.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAllMetadata()
    {
        return $this->metadata;
    }

    /**
     * Get the MessageMetadata for a participant.
     *
     * @param  ParticipantInterface $participant
     * @return MessageMetadata|null
     */
    public function getMetadataForParticipant(ParticipantInterface $participant)
    {
        foreach ($this->metadata as $meta) {
            /** @var MessageMetadata $meta */
            if ($meta->getParticipant() === $participant) {
                return $meta;
            }
        }

        return null;
    }

    /**
     * @param ParticipantInterface $participant
     * @return Boolean
     */
    public function isReadByParticipant(ParticipantInterface $participant)
    {
        if ($meta = $this->getMetadataForParticipant($participant)) {
            return $meta->getIsRead();
        }

        return false;
    }

    /**
     * @param ParticipantInterface $participant
     * @param Boolean $isRead
     * @throws \InvalidArgumentException
     */
    public function setIsReadByParticipant(ParticipantInterface $participant, $isRead)
    {
        if (!$meta = $this->getMetadataForParticipant($participant)) {
            throw new \InvalidArgumentException('No metadata exists for participant');
        }

        $meta->setIsRead($isRead);
    }
}
