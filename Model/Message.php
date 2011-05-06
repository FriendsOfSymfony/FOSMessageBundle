<?php

namespace Ornicar\MessageBundle\Model;

use FOS\UserBundle\Model\User;
use DateTime;

abstract class Message
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
     * @var User
     */
    protected $from = null;

    /**
     * User who received the message
     *
     * @var User
     * @assert:NotBlank(message="Please enter a valid recipient")
     */
    protected $to = null;

    /**
     * Text body of the message
     *
     * @var string
     * @assert:NotBlank(message="Please enter a message")
     * @assert:MinLength(limit=10, message="Too short")
     */
    protected $body = null;

    /**
     * Text subject of the message
     *
     * @var string
     * @assert:NotBlank(message="Please enter a subject")
     * @assert:MinLength(limit=5, message="Too short")
     */
    protected $subject = null;

    /**
     * Date when the message was sent
     *
     * @var DateTime
     */
    protected $createdAt = null;

    /**
     * Whether or not the message has been read
     *
     * @var bool
     */
    protected $isRead = false;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Gets the message id
     *
     * @return mixed
     **/
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getIsRead()
    {
      return $this->isRead;
    }

    /**
     * @param  bool
     * @return null
     */
    public function setIsRead($isRead)
    {
      $this->isRead = $isRead;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
      return $this->createdAt;
    }

    /**
     * @param  DateTime
     * @return null
     */
    public function setCreatedAt($createdAt)
    {
      $this->createdAt = $createdAt;
    }
    /**
     * @return string
     */
    public function getSubject()
    {
      return $this->subject;
    }

    /**
     * @param  string
     * @return null
     */
    public function setSubject($subject)
    {
      $this->subject = $subject;
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
     * @return User
     */
    public function getFrom()
    {
      return $this->from;
    }

    /**
     * @param  User
     * @return null
     */
    public function setFrom(User $from)
    {
      $this->from = $from;
    }

    /**
     * @return User
     */
    public function getTo()
    {
      return $this->to;
    }

    /**
     * @param  User
     * @return null
     */
    public function setTo(User $to)
    {
      $this->to = $to;
    }

    public function isVisibleBy(User $user)
    {
        return $user->isUser($this->getTo()) || $user->isUser($this->getFrom());
    }
}
