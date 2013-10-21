<?php

namespace FOS\MessageBundle\Tests\MessageBuilder\Orm;

use FOS\MessageBundle\Entity\Thread;
use FOS\MessageBundle\Entity\Message;
use FOS\MessageBundle\Entity\MessageMetadata;
use FOS\MessageBundle\Entity\ThreadMetadata;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\MessageBuilder\Orm\NewThreadBuilder;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ThreadInterface;

/**
 * Some helper functions for the orm messagebuilders
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class OrmBuilderTestHelpers extends \PHPUnit_Framework_TestCase
{
    protected $builder;

    const BODY = "body";
    const SUBJECT = "subject";

    /**
     * This builds a new thread we can use in our tests;
     *
     * @param ParticipantInterface $sender    The sender of the new thread
     * @param ParticipantInterface $recipient The recipient of the new thread
     *
     * @return ThreadInterface
     */
    protected function getNewThread($sender, $recipient)
    {
        $c = $this->getClasses();

        $this->builder = new NewThreadBuilder();
        $this->builder->setMessageClass($c['message']);
        $this->builder->setThreadClass($c['thread']);
        $this->builder->setMessageMetaClass($c['messageMeta']);
        $this->builder->setThreadMetaClass($c['threadMeta']);

        $this->builder->setCreatedAt($this->getDateOfNewThread());
        $this->builder->setBody(self::BODY);
        $this->builder->setIsSpam(false);
        $this->builder->setSender($sender);
        $this->builder->setRecipients(array($recipient));
        $this->builder->setSubject(self::SUBJECT);

        return $this->builder->build();
    }

    protected function getDateOfNewThread()
    {
        return new \DateTime('2012-10-05 00:00:00');
    }

    /**
     * Gets a participant with id sender_id
     *
     * @return ParticipantInterface
     */
    protected function getSender()
    {
        return new Sender();
    }

    /**
     * Gets a participant with id receiver_id
     *
     * @return ParticipantInterface
     */
    protected function getRecipient()
    {
        return new Recipient();
    }

    protected function getAnotherRecipient()
    {
        return new AnotherRecipient();
    }

    /**
     * Helper function to get autocompletion
     *
     * @param MessageInterface $message The message
     *
     * @return MessageInterface The same message with auto completion
     */
    protected function getMessage($message)
    {
        return $message;
    }

    protected function getClasses()
    {
        $class['message'] = '\FOS\MessageBundle\Tests\MessageBuilder\Orm\TestMessage';
        $class['messageMeta'] = '\FOS\MessageBundle\Tests\MessageBuilder\Orm\TestMessageMetadata';
        $class['thread'] = '\FOS\MessageBundle\Tests\MessageBuilder\Orm\TestThread';
        $class['threadMeta'] = '\FOS\MessageBundle\Tests\MessageBuilder\Orm\TestThreadMetadata';

        return $class;
    }
}

class TestMessage extends Message
{

}

class TestMessageMetadata extends MessageMetadata
{

}

class TestThread extends Thread
{

}

class TestThreadMetadata extends ThreadMetadata
{

}

class Sender implements ParticipantInterface
{
    public function getId()
    {
        return 'sender_id';
    }
}

class Recipient implements ParticipantInterface
{
    public function getId()
    {
        return 'receiver_id';
    }
}

class AnotherRecipient implements ParticipantInterface
{

    public function getId()
    {
        return 'receiver_id2';
    }
}
