<?php

namespace Bundle\Ornicar\MessageBundle\Model;

use Bundle\FOS\UserBundle\Model\User;
use DateTime;

class Message
{
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
     * @validation:NotBlank(message="Missing to")
     */
    protected $to = null;

    /**
     * Text body of the message
     *
     * @var string
     * @validation:NotBlank(message="Please write a message")
     * @validation:MinLength(limit=4, message="Just a little too short.")
     */
    protected $body = null;

    /**
     * Text subject of the message
     *
     * @var string
     * @validation:NotBlank(message="Please write a message")
     * @validation:MinLength(limit=2, message="Just a little too short.")
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
    public function setFrom($from)
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
    public function setTo($to)
    {
      $this->to = $to;
    }
}
