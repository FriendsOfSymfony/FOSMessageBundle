<?php

namespace FOS\MessageBundle\Model;

use FOS\MessageBundle\Model\ParticipantInterface;

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
     * @return ThreadInterface
     */
    public function getThread();

    /**
     * Sets the thread
     * 
     * @param ThreadInterface $thread The thread
     */
    public function setThread(ThreadInterface $thread);

    /**
     * Gets the datetime when the message was created
     * 
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Gets the body of the message
     * 
     * @return string
     */
    public function getBody();

    /**
     * Sets the body of the message
     * 
     * @param string $body
     */
    public function setBody($body);

    /**
     * @return ParticipantInterface
     */
    public function getSender();

    /**
     * Sets the sender of the message
     * 
     * @param ParticipantInterface $sender
     */
    public function setSender(ParticipantInterface $sender);

    /**
     * Sets the creation time of the message
     *
     * @param \DateTime $createdAt the datetime of the new message
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Gets message metadata for a given participant
     *
     * @param ParticipantInterface $participant the participant for who we get teh meta
     */
    public function getMetadataForParticipant(ParticipantInterface $participant);
}
