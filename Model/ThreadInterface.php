<?php

namespace Ornicar\MessageBundle\Model;

use Ornicar\MessageBundle\Model\ParticipantInterface;

interface ThreadInterface extends ReadableInterface
{
    /**
     * Gets the message unique id
     *
     * @return mixed
     **/
    function getId();

    /**
     * @return string
     */
    function getSubject();

    /**
     * @param  string
     * @return null
     */
    function setSubject($subject);

    /**
     * Gets the messages contained in the thread
     *
     * @return array of MessageInterface
     */
    function getMessages();

    /**
     * Adds a new message to the thread
     *
     * @param MessageInterface $message
     */
    function addMessage(MessageInterface $message);

    /**
     * Gets the last message of the thread
     *
     * @return MessageInterface the last message
     */
    function getLastMessage();

    /**
     * Gets the users participating in this conversation
     *
     * @return array of ParticipantInterface
     */
    function getParticipants();

    /**
     * Tells if the user participates to the conversation
     *
     * @param ParticipantInterface $user
     * @return boolean
     */
    function isParticipant(ParticipantInterface $user);

    /**
     * Adds a participant to the thread
     * If it already exists, nothing is done.
     *
     * @param ParticipantInterface $participant
     * @return null
     */
    function addParticipant(ParticipantInterface $participant);

    /**
     * Tells if this thread is deleted by this participant
     *
     * @return bool
     */
    function isDeletedByParticipant(ParticipantInterface $participant);

    /**
     * Sets whether or not this participant has deleted this thread
     *
     * @param ParticipantInterface $participant
     * @param boolean $isDeleted
     */
    function setIsDeletedByParticipant(ParticipantInterface $participant, $isDeleted);
}
