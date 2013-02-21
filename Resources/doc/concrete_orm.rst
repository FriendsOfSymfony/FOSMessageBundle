Concrete classes for Doctrine ORM
=================================

The ORM implementation does not provide concrete models. You must create Message
MessageMetadata, Thread and ThreadMetadata classes in your application::

Message class
-------------

::

    // src/Acme/MessageBundle/Entity/Message.php

    namespace Acme\MessageBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;

    use FOS\MessageBundle\Entity\Message as BaseMessage;
    use FOS\MessageBundle\Model\ThreadInterface;
    use FOS\MessageBundle\Model\ParticipantInterface;
    use FOS\MessageBundle\Model\MessageMetadata as ModelMessageMetadata;

    /**
     * @ORM\Entity
     */
    class Message extends BaseMessage
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\generatedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @ORM\ManyToOne(targetEntity="Acme\MessageBundle\Entity\Thread", inversedBy="messages")
         * @ORM\JoinColumn(name="thread_id", referencedColumnName="id")
         */
        protected $thread;

        /**
         * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User")
         * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
         */
        protected $sender;

        /**
         * @ORM\OneToMany(targetEntity="Acme\MessageBundle\Entity\MessageMetadata", mappedBy="message", cascade={"all"})
         */
        protected $metadata;

        public function __construct()
        {
            parent::__construct();

            $this->metadata  = new ArrayCollection();
        }

        public function setThread(ThreadInterface $thread) {
    		$this->thread = $thread;
    		return $this;
    	}
    
    	public function setSender(ParticipantInterface $sender) {
    		$this->sender = $sender;
    		return $this;
    	}
    
    	public function addMetadata(ModelMessageMetadata $meta) {
    	    $meta->setMessage($this);
    	    parent::addMetadata($meta);
    	}

    }

MessageMetadata class
---------------------

::

    // src/Acme/MessageBundle/Entity/MessageMetadata.php

    namespace Acme\MessageBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    use FOS\MessageBundle\Entity\MessageMetadata as BaseMessageMetadata;
    use FOS\MessageBundle\Model\MessageInterface;
    use FOS\MessageBundle\Model\ParticipantInterface;

    /**
     * @ORM\Entity
     * @ORM\Table(name="message_message_metadata")
     */
    class MessageMetadata extends BaseMessageMetadata
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\generatedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @ORM\ManyToOne(targetEntity="Acme\MessageBundle\Entity\Message", inversedBy="metadata")
         */
        protected $message;

        /**
         * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User")
         */
        protected $participant;

        public function setMessage(MessageInterface $message) {
    	    $this->message = $message;
    	    return $this;
    	}
    
    	public function setParticipant(ParticipantInterface $participant) {
    		$this->participant = $participant;
    		return $this;
    	}

    }

Thread class
------------

::

    // src/Acme/MessageBundle/Entity/Thread.php

    namespace Acme\MessageBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;

    use FOS\MessageBundle\Entity\Thread as BaseThread;
    use FOS\MessageBundle\Model\ParticipantInterface;
    use FOS\MessageBundle\Model\MessageInterface;
    use FOS\MessageBundle\Model\ThreadMetadata as ModelThreadMetadata;

    /**
     * @ORM\Entity
     * @ORM\Table(name="message_thread")
     */
    class Thread extends BaseThread
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\generatedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User")
         */
        protected $createdBy;

        /**
         * @ORM\OneToMany(targetEntity="Acme\MessageBundle\Entity\Message", mappedBy="thread")
         */
        protected $messages;

        /**
         * @ORM\OneToMany(targetEntity="Acme\MessageBundle\Entity\ThreadMetadata", mappedBy="thread", cascade={"all"})
         */
        protected $metadata;

        public function __construct()
        {
            parent::__construct();

            $this->messages = new ArrayCollection();
        }

        public function setCreatedBy(ParticipantInterface $participant) {
    		$this->createdBy = $participant;
    		return $this;
    	}
    
    	function addMessage(MessageInterface $message) {
    		$this->messages->add($message);
    	}
    
    	public function addMetadata(ModelThreadMetadata $meta) {
    	    $meta->setThread($this);
    	    parent::addMetadata($meta);
    	}

    }

ThreadMetadata class
--------------------

::

    // src/Acme/MessageBundle/Entity/ThreadMetadata.php

    namespace Acme\MessageBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    use FOS\MessageBundle\Entity\ThreadMetadata as BaseThreadMetadata;
    use FOS\MessageBundle\Model\ThreadInterface;
    use FOS\MessageBundle\Model\ParticipantInterface;

    /**
     * @ORM\Entity
     * @ORM\Table(name="message_thread_metadata")
     */
    class ThreadMetadata extends BaseThreadMetadata
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\generatedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @ORM\ManyToOne(targetEntity="Acme\MessageBundle\Entity\Thread", inversedBy="metadata")
         */
        protected $thread;

        /**
         * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User")
         */
        protected $participant;

        public function setThread(ThreadInterface $thread) {
    	    $this->thread = $thread;
    	}
    
    	public function setParticipant(ParticipantInterface $participant) {
    	    $this->participant = $participant;
    	    return $this;
    	}

    }

Configure your application::

    # app/config/config.yml

    fos_message:
        db_driver: orm
        thread_class: Acme\MessageBundle\Entity\Thread
        message_class: Acme\MessageBundle\Entity\Message
