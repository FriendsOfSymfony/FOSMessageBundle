<?php

namespace FOS\MessageBundle\Model;

/**
 * Message model
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface MessageInterface extends ReadableInterface
{
    /**
     * Gets the message unique id
     *
     * @return mixed
     */
    public function getId();

    /**
     * Gets the thread this message belongs to
     * 
     * @return ThreadInterface
     */
    public function getThread();

    /**
     * Set the thread this message belongs to
     * 
     * @param ThreadInterface     * 
     */
    public function setThread(ThreadInterface $thread);

    /**
     * Gets the time this message was created
     * 
     * @return DateTime
     */
    public function getCreatedAt();

   /**
    * Sets the creation date of the message
    *
    * @param \DateTime
    */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Gets the body of the message
     * 
     * @return string
     */
    public function getBody();

    /**
     * Sets the body of the message
     * 
     * @param string
     */
    public function setBody($body);

    /**
     * Returns the sender of the message
     *
     * @return ParticipantInterface
     */
    public function getSender();

    /**
     * Sets the sender of the message
     *
     * @param ParticipantInterface
     */
    public function setSender(ParticipantInterface $sender);

    /**
     * Get the MessageMetadata for a participant.
     *
     * @param ParticipantInterface $participant
     * 
     * @return MessageMetadata|null
     */
    public function getMetadataForParticipant(ParticipantInterface $participant);
}
