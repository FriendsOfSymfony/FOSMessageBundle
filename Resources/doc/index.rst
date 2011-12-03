Provides messenging features for your Symfony2 application.

The persistence is storage agnostic. Any backend can be implemented: Doctrine, Propel, and others.
Actually the Doctrine MongoDB implementation only is complete.

MessageBundle supports threads, spam detection, soft deletion and messenging permissions.

MessageBundle can be used with FOS\UserBundle, but it is not required.

Installation
============

Add MessageBundle to your src/ dir
-------------------------------------

::

    $ git submodule add git://github.com/Ornicar/MessageBundle.git vendor/bundles/Ornicar/MessageBundle

Add the Ornicar namespace to your autoloader
----------------------------------------

::

    // app/autoload.php

    $loader->registerNamespaces(array(
        'Ornicar' => __DIR__.'/../vendor/bundles',
        // your other namespaces
    );

Add MessageBundle to your application kernel
-----------------------------------------

::

    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...
            new Ornicar\MessageBundle\OrnicarMessageBundle(),
            // ...
        );
    }

Configure your project
----------------------

You have to include the MessageBundle in your Doctrine mapping configuration,
along with the bundle containing your custom Thread and Message classes::

    # app/config/config.yml

    doctrine_mongo_db:
        document_managers:
            default:
                mappings:
                    OrnicarMessageBundle: ~
                    # your other bundles

The above example assumes a MongoDB configuration, but the `mappings` configuration
block would be the same for ORM.

Choose your user
----------------

The message senders and recipients are called *participants* of the thread.
MessageBundle will only refer to them using the `ParticipantInterface`.
This allows you to use any user class. Just make it implement this interface.

For exemple, if your user class is ``Acme\UserBundle\Document\User``::

    // src/Acme/UserBundle/Document/User.php

    use Ornicar\MessageBundle\Model\ParticipantInterface;

    /**
     * @MongoDB\Document
     */
    class User implements ParticipantInterface
    {
        // your code here
    }

If you need a bundle providing a base user, see http://github.com/FriendsOfSymfony/FOSUserBundle

MongoDB
~~~~~~~

The MongoDB implementation does not provide concrete models.
You must create a Thread class and a Message class in your application.

Thread class::

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
         * @MongoDB\ReferenceMany(targetDocument="Acme\UserBundle\Document\User")
         */
         protected $participants;

        /**
         * @MongoDB\ReferenceOne(targetDocument="Acme\UserBundle\Document\User")
         */
         protected $createdBy;
    }

Message class::

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
         * @MongoDB\ReferenceOne(targetDocument="Acme\MessageBundle\Document\Thread")
         */
         protected $thread;

        /**
         * @MongoDB\ReferenceOne(targetDocument="Acme\UserBundle\Document\User")
         */
         protected $sender;
    }

Configure your application::

    # app/config/config.yml

    ornicar_message:
        db_driver: mongodb
        thread_class: Acme\MessageBundle\Document\Thread
        message_class: Acme\MessageBundle\Document\Message

ORM
~~~

The ORM implementation does not provide concrete models.
You must create a Message, MessageMetadata, Thread and ThreadMetadata classes in your application::

    // src/Acme/MessageBundle/Entity/Message.php

    namespace Acme\MessageBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    use Ornicar\MessageBundle\Entity\Message as BaseMessage;

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

    // src/Acme/MessageBundle/Entity/MessageMetadata.php

    namespace Acme\MessageBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    use Ornicar\MessageBundle\Entity\MessageMetadata as BaseMessageMetadata;

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

    // src/Acme/MessageBundle/Entity/Thread.php

    namespace Acme\MessageBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    use Ornicar\MessageBundle\Entity\Thread as BaseThread;

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

    // src/Acme/MessageBundle/Entity/ThreadMetadata.php

    namespace Acme\MessageBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    use Ornicar\MessageBundle\Entity\ThreadMetadata as BaseThreadMetadata;

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

    ornicar_message:
        db_driver: orm
        thread_class: Acme\MessageBundle\Entity\Thread
        message_class: Acme\MessageBundle\Entity\Message


Register routing
----------------

You will probably want to include the builtin routes.

In YAML::

    # app/config/routing.yml

    ornicar_message:
        resource: "@OrnicarMessageBundle/Resources/config/routing.xml"

Or if you prefer XML::

    # app/config/routing.xml

    <import resource="@OrnicarMessageBundle/Resources/config/routing.xml"/>

Basic Usage
===========

Have a look to the default controller to learn how to use the messenging services::

    Controller\MessageController.php

Get user threads
----------------

Get the threads in the inbox of the authenticated user::

    $provider = $container->get('ornicar_message.provider');

    $threads = $provider->getInboxThreads();

And the threads in the sentbox::

    $threads = $provider->getSentThreads();

To get a single thread, check it belongs to the authenticated user and mark it as read::

    $thread = $provider->getThread($threadId);

Manipulate threads
------------------

See ``Ornicar\\MessageBundle\\Model\\ThreadInterface`` for the complete list of available methods::

    // Print the thread subject
    echo $thread->getSubject();

    // Get the tread participants
    $participants = $thread->getParticipants();

    // Know if this participant has read this thread
    if ($thread->isReadByParticipant($participant))

    // Know if this participant has deleted this thread
    if ($thread->isDeletedByParticipant($participant))


Manipulate messages
-------------------

See ``Ornicar\\MessageBundle\\Model\\MessageInterface`` for the complete list of available methods::

    // Print the message body
    echo $message->getBody();

    // Get the message sender participant
    $sender = $message->getSender();

    // Get the message thread
    $thread = $message->getThread();

    // Know if this participant has read this message
    if ($message->isReadByParticipant($participant))

Compose a message
--------------

Create a new message thread::

    $composer = $container->get('ornicar_message.composer');

    $message = $composer->newThread()
        ->setSender($jack)
        ->addRecipient($clyde)
        ->setSubject('Hi there')
        ->setBody('This is a test message')
        ->getMessage();

And to reply to this thread::

    $message = $composer->reply($thread)
        ->setSender($clyde)
        ->setBody('This is the answer to the test message')
        ->getMessage();

Note that when replying, we don't need to provide the subject nor the recipient.
Because they are the attributes of the thread, which already exists.

Send a message
--------------

Nothing's easier than sending the message you've just composed::

    $sender = $container->get('ornicar_message.sender');

    $sender->send($message);

Templating
==========

MessageBundle provides a few twig functions::

    {# template.html.twig #}

    {# Know if a message is read by the authenticated participant #}
    {% if not ornicar_message_is_read(message) %} This message is new! {% endif %}

    {# Know if a thread is read by the authenticated participant. Yes, it's the same function. #}
    {% if not ornicar_message_is_read(thread) %} This thread is new! {% endif %}

    {# Get the number of new threads for the authenticated participant #}
    You have {{ ornicar_message_nb_unread() }} new messages

Spam detection
==============

Using Akismet
-------------

Install AkismetBundle (http://github.com/ornicar/AkismetBundle).

Then, set the spam detector service accordingly::

    # app/config/config.yml

        ornicar_message:
            spam_detector: ornicar_message.akismet_spam_detector

Other strategy
--------------

You can use any spam dectetor service, including one of your own.
The class must implement ``Ornicar\MessageBundle\SpamDetection\SpamDetectorInterface``.

Messenging permissions
======================

You can change the security logic by replacing the ``authorizer`` service::

    # app/config/config.yml

        ornicar_message:
            authorizer: acme_message.authorizer

Your class must implement ``Ornicar\MessageBundle\Security\AuthorizerInterface``::

    interface AuthorizerInterface
    {
        /**
        * Tells if the current user is allowed
        * to see this thread
        *
        * @param ThreadInterface $thread
        * @return boolean
        */
        function canSeeThread(ThreadInterface $thread);

        /**
        * Tells if the current participant is allowed
        * to delete this thread
        *
        * @param ThreadInterface $thread
        * @return boolean
        */
        function canDeleteThread(ThreadInterface $thread);

        /**
        * Tells if the current participant is allowed
        * to send a message to this other participant
        *
        * $param ParticipantInterface $participant the one we want to send a message to
        * @return boolean
        */
        function canMessageParticipant(ParticipantInterface $participant);
    }

You can tell whether the user can see or delete a thread, and if he can send a new message to another user.
See the default implementation in ``Ornicar\MessageBundle\Security\Authorizer``.


Listening to events
===================

This bundles dispatches event when notable actions are performed.

See ``Ornicar\MessageBundle\Event\OrnicarMessageEvents`` for a documented
list of the available events.

Configuration
=============

All configuration options are listed below::

    # app/config/config.yml

    ornicar_message
        db_driver:              mongodb
        thread_class:           Acme\MessageBundle\Document\Thread      
        message_class:          Acme\MessageBundle\Document\Message    
        message_manager:        ornicar_message.message_manager         # See ModelManager\MessageManagerInterface
        thread_manager:         ornicar_message.thread_manager          # See ModelManager\ThreadManagerInterface
        sender:                 ornicar_message.sender                  # See Sender\SenderInterface
        composer:               ornicar_message.composer                # See Composer\ComposerInterface
        provider:               ornicar_message.provider                # See Provider\ProviderInterface
        participant_provider:   ornicar_message.participant_provider    # See Security\ParticipantProviderInterface
        authorizer:             ornicar_message.authorizer              # See Security\AuthorizerInterface
        message_reader:         ornicar_message.message_reader          # See Reader\ReaderInterface
        thread_reader:          ornicar_message.thread_reader           # See Reader\ReaderInterface
        deleter:                ornicar_message.deleter                 # See Deleter\DeleterInterface
        spam_detector:          ornicar_message.noop_spam_detector      # See SpamDetection\SpamDetectorInterface
        twig_extension:         ornicar_message.twig_extension          # See Twig\Extension\MessageExtension
        search:
            finder:             ornicar_message.search_finder           # See Finder\FinderInterface
            query_factory:      ornicar_message.search_query_factory    # See Finder\QueryFactoryInterface
            query_parameter:    'q'                                     # Request query parameter containing the term
        new_thread_form:
            factory:            ornicar_message.new_thread_form.factory # See FormFactory\NewThreadMessageFormFactory
            type:               ornicar_message.new_thread_form.type    # See FormType\NewThreadMessageFormType
            handler:            ornicar_message.new_thread_form.handler # See FormHandler\NewThreadMessageFormHandler
            name:               message
        reply_form:
            factory:            ornicar_message.reply_form.factory      # See FormFactory\ReplyMessageFormFactory
            type:               ornicar_message.reply_form.type         # See FormType\ReplyMessageFormType
            handler:            ornicar_message.reply_form.handler      # See FormHandler\ReplyMessageFormHandler
            name:               message

Implement a new persistence backend
===================================

I need your help for the ORM - and more - implementations.

Implementation
--------------

To provide a new backend implementation, you must implement these interfaces:

- ``Model/ThreadInterface.php``
- ``Model/MessageInterface.php``
- ``ModelManager/ThreadManagerInterface.php``
- ``ModelManager/MessageManagerInterface.php``

MongoDB implementation examples:

- ``Document/Thread.php``
- ``Document/Message.php``
- ``DocumentManager/ThreadManager.php``
- ``DocumentManager/MessageManager.php``

Note that the MongoDB manager classes only contain MongoDB specific logic,
backend agnostic logic lives in the abstract managers.


Mapping
-------

You may also need to define mappings.

MongoDB mapping examples:

- ``src/Ornicar/MessageBundle/Resources/config/doctrine/thread.mongodb.xml``
- ``src/Ornicar/MessageBundle/Resources/config/doctrine/message.mongodb.xml``
