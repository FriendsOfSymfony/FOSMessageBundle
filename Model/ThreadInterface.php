<?php

namespace Ornicar\MessageBundle\Model;

use FOS\UserBundle\Model\UserInterface;

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
     * @return array of UserInterface
     */
    function getParticipants();

    /**
     * Tells if the user participates to the conversation
     *
     * @param UserInterface $user
     * @return boolean
     */
    function isParticipant(UserInterface $user);

    /**
     * Adds a participant to the thread
     * If it already exists, nothing is done.
     *
     * @param UserInterface $participant
     * @return null
     */
    function addParticipant(UserInterface $participant);

    /**
     * Tells if this thread is deleted by this participant
     *
     * @return bool
     */
    function isDeletedByParticipant(UserInterface $participant);

    /**
     * Sets whether or not this participant has deleted this thread
     *
     * @param UserInterface $participant
     * @param boolean $isDeleted
     */
    function setIsDeletedByParticipant(UserInterface $participant, $isDeleted);
}
