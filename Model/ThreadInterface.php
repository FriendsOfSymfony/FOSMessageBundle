<?php

namespace Ornicar\MessageBundle\Model;

use FOS\UserBundle\Model\UserInterface;

interface ThreadInterface
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
     * Tells if all messages this user is the recipient of are read
     *
     * @return bool
     */
    function isReadByUser(UserInterface $user);
}
