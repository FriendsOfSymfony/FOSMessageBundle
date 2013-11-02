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
     * @return ThreadInterface
     */
    public function getThread();

    /**
     * @param ThreadInterface
     */
    public function setThread(ThreadInterface $thread);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string
     */
    public function setBody($body);

    /**
     * @return ParticipantInterface
     */
    public function getSender();

    /**
     * @param ParticipantInterface
     */
    public function setSender(ParticipantInterface $sender);

    /**
     * Get the MessageMetadata for a participant.
     *
     * @param  ParticipantInterface $participant
     * @return MessageMetadata|null
     */
    public function getMetadataForParticipant(ParticipantInterface $participant);
}
