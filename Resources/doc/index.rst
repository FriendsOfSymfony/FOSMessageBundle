Provides messenging features for your Symfony2 application.

The persistence is storage agnostic. Any backend can be implemented: Doctrine, Propel, and others.
Actually the Doctrine MongoDB implementation only is complete.

MessageBundle supports threads.

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

        public function getId()
        {
            return $this->id;
        }

If you need a bundle providing a base user, see http://github.com/FriendsOfSymfony/UserBundle

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

The ORM backend is not implemented, yet. Send your PR.


Register routing
----------------

You will probably want to include the builtin routes.

In YAML::

    # app/config/routing.yml

    ornicar_message:
        resource: "@OrnicarMessageBundle/Resources/config/routing.yml"

Or if you prefer XML::

    # app/config/routing.xml

    <import resource="@OrnicarMessageBundle/Resources/config/routing.yml"/>

Basic Usage
===========

Have a look to the default controller to learn how to use the messenging services::

    Controller\MessageController.php

Get messages
------------

Get the threads in the inbox of the authenticated user::

    $provider = $container->get('ornicar_message.provider');

    $threads = $provider->getInboxThreads();

And the threads in the sentbox::

    $threads = $provider->getSentThreads();

To get a single thread, check it belongs to the authenticated user and mark it as read::

    $thread = $provider->getThread($threadId);

Send a message
--------------

Create a new message thread::

    $composer = $container->get('ornicar_message.composer');

    $message = $composer->compose()
        ->setSender($jack)
        ->setRecipient($clyde)
        ->setSubject('Hi there')
        ->setBody('This is a test message')
        ->send();

And to reply to this message::

    $composer->compose()
        ->inReplyToThread($message->getThread())
        ->setSender($clyde)
        ->setBody('This is the answer to the test message')
        ->send();

Note that when replying, we don't need to provide the subject nor the recipient.
Because they are the attributes of the thread, which already exists.

Configuration
=============

All configuration options are listed below::

    # app/config/config.yml

    ornicar_message
        db_driver:          mongodb
        thread_class:       Acme\MessageBundle\Document\Thread      
        message_class:      Acme\MessageBundle\Document\Message    
        message_manager:    ornicar_message.message_manager         # See ModelManager\MessageManagerInterface
        thread_manager:     ornicar_message.thread_manager          # See ModelManager\ThreadManagerInterface
        sender:             ornicar_message.sender                  # See Sender\SenderInterface
        composer:           ornicar_message.composer                # See Composer\ComposerInterface
        provider:           ornicar_message.provider                # See Provider\ProviderInterface
        authorizer:         ornicar_message.authorizer              # See Authorizer\AuthorizerInterface
        message_reader:     ornicar_message.message_reader          # See Reader\ReaderInterface
        thread_reader:      ornicar_message.thread_reader           # See Reader\ReaderInterface
        deleter:            ornicar_message.deleter                 # See Deleter\DeleterInterface
        search:
            finder:         ornicar_message.search_finder           # See Finder\FinderInterface
            query_factory:  ornicar_message.search_query_factory    # See Finder\QueryFactoryInterface
            query_parameter: 'q'                                    # Request query parameter containing the term
        new_thread_form:
            factory:        ornicar_message.new_thread_form.factory # See FormFactory\NewThreadMessageFormFactory
            type:           ornicar_message.new_thread_form.type    # See FormType\NewThreadMessageFormType
            handler:        ornicar_message.new_thread_form.handler # See FormHandler\NewThreadMessageFormHandler
            name:           message
        reply_form:
            factory:        ornicar_message.reply_form.factory      # See FormFactory\ReplyMessageFormFactory
            type:           ornicar_message.reply_form.type         # See FormType\ReplyMessageFormType
            handler:        ornicar_message.reply_form.handler      # See FormHandler\ReplyMessageFormHandler
            name:           message

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
