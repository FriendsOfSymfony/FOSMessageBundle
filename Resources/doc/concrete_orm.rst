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

    use FOS\MessageBundle\Entity\Message as BaseMessage;

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
         * @ORM\ManyToOne(targetEntity="Acme\MessageBundle\Entity\Thread")
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
    }

MessageMetadata class
---------------------

::

    // src/Acme/MessageBundle/Entity/MessageMetadata.php

    namespace Acme\MessageBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    use FOS\MessageBundle\Entity\MessageMetadata as BaseMessageMetadata;

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
    }

Thread class
------------

::

    // src/Acme/MessageBundle/Entity/Thread.php

    namespace Acme\MessageBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    use FOS\MessageBundle\Entity\Thread as BaseThread;

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

            $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
        }
    }

ThreadMetadata class
--------------------

::

    // src/Acme/MessageBundle/Entity/ThreadMetadata.php

    namespace Acme\MessageBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    use FOS\MessageBundle\Entity\ThreadMetadata as BaseThreadMetadata;

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

    }

Configure your application::

    # app/config/config.yml

    fos_message:
        db_driver: orm
        thread_class: Acme\MessageBundle\Entity\Thread
        message_class: Acme\MessageBundle\Entity\Message