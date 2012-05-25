Concrete classes for MongoDB:
=============================

The MongoDB implementation does not provide concrete models. You must create
Thread and Message classes in your application.

Thread class
------------

::

    // src/Acme/MessageBundle/Document/Thread.php

    namespace Acme\MessageBundle\Document;

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Ornicar\MessageBundle\Document\Thread as BaseThread;

    /**
     * @MongoDB\Document
     */
    class Thread extends BaseThread
    {
        /**
         * @MongoDB\Id
         */
        protected $id;

        /**
         * @MongoDB\ReferenceMany(targetDocument="Acme\MessageBundle\Document\Message")
         */
        protected $messages;

        /**
         * @MongoDB\EmbedMany(targetDocument="Acme\MessageBundle\Document\ThreadMetadata")
         */
        protected $metadata;

        /**
         * @MongoDB\ReferenceMany(targetDocument="Acme\UserBundle\Document\User")
         */
        protected $participants;

        /**
         * @MongoDB\ReferenceOne(targetDocument="Acme\UserBundle\Document\User")
         */
        protected $createdBy;
    }

Message class
-------------

::

    // src/Acme/MessageBundle/Document/Message.php

    namespace Acme\MessageBundle\Document;

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Ornicar\MessageBundle\Document\Message as BaseMessage;

    /**
     * @MongoDB\Document
     */
    class Message extends BaseMessage
    {
        /**
         * @MongoDB\Id
         */
        protected $id;

        /**
         * @MongoDB\EmbedMany(targetDocument="Acme\MessageBundle\Document\MessageMetadata")
         */
        protected $metadata;

        /**
         * @MongoDB\ReferenceOne(targetDocument="Acme\MessageBundle\Document\Thread")
         */
        protected $thread;

        /**
         * @MongoDB\ReferenceOne(targetDocument="Acme\UserBundle\Document\User")
         */
        protected $sender;
    }

ThreadMetaData class
--------------------

::

    <?php
    namespace Mashup\MessageBundle\Document;

    use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
    use Ornicar\MessageBundle\Document\ThreadMetadata as BaseThreadMetadata;

    /**
     * @ODM\EmbeddedDocument
     */
    class ThreadMetadata extends BaseThreadMetadata
    {
        /**
         * @ODM\ReferenceOne(targetDocument="Mashup\UserBundle\Document\User")
         */
        protected $participant;
    }

MessageMetaData class
---------------------

::

    <?php
    namespace Mashup\MessageBundle\Document;

    use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
    use Ornicar\MessageBundle\Document\MessageMetadata as BaseMessageMetadata;

    /**
     * @ODM\EmbeddedDocument
     */
    class MessageMetadata extends BaseMessageMetadata
    {
        /**
         * @ODM\ReferenceOne(targetDocument="Mashup\UserBundle\Document\User")
         */
        protected $participant;
    }

Configure your application::

    # app/config/config.yml

    ornicar_message:
        db_driver: mongodb
        thread_class: Acme\MessageBundle\Document\Thread
        message_class: Acme\MessageBundle\Document\Message