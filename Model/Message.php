<?php

namespace FOS\MessageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Abstract message model.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class Message implements MessageInterface
{
    /**
     * Unique id of the message.
     *
     * @var mixed
     */
    protected $id;

    /**
     * User who sent the message.
     *
     * @var ParticipantInterface
     */
    protected $sender;

    /**
     * Text body of the message.
     *
     * @var string
     */
    protected $body;

    /**
     * Date when the message was sent.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Thread the message belongs to.
     *
     * @var ThreadInterface
     */
    protected $thread;

    /**
     * Collection of MessageMetadata.
     *
     * @var Collection|MessageMetadata[]
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

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * {@inheritdoc}
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * {@inheritdoc}
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * {@inheritdoc}
     */
    public function setSender(ParticipantInterface $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Gets the created at timestamp.
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->getCreatedAt()->getTimestamp();
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
     * Get the MessageMetadata for a participant.
     *
     * @param ParticipantInterface $participant
     *
     * @return MessageMetadata
     */
    public function getMetadataForParticipant(ParticipantInterface $participant)
    {
        foreach ($this->metadata as $meta) {
            if ($meta->getParticipant()->getId() == $participant->getId()) {
                return $meta;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadByParticipant(ParticipantInterface $participant)
    {
        if ($meta = $this->getMetadataForParticipant($participant)) {
            return $meta->getIsRead();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsReadByParticipant(ParticipantInterface $participant, $isRead)
    {
        if (!$meta = $this->getMetadataForParticipant($participant)) {
            throw new \InvalidArgumentException(sprintf('No metadata exists for participant with id "%s"', $participant->getId()));
        }

        $meta->setIsRead($isRead);
    }
}
