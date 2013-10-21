<?php

namespace FOS\MessageBundle\Tests\MessageBuilder\Orm;

use FOS\MessageBundle\MessageBuilder\Orm\NewThreadBuilder;
use FOS\MessageBundle\Model\ParticipantInterface;

/**
 * Test file for the new thread builder class
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class NewThreadBuilderTest extends OrmBuilderTestHelpers
{
    /**
     * A NewThreadBuilder instance
     *
     * @var NewThreadBuilder
     */
    protected $builder;

    /**
     * Sender of the new thread
     *
     * @var ParticipantInterface
     */
    protected $sender;

    /**
     * Recipient of the thread
     *
     * @var ParticipantInterface
     */
    protected $recipient;

    public function setUp()
    {
        $this->sender = $this->getSender();
        $this->recipient = $this->getRecipient();
        $c = $this->getClasses();
        $this->builder = new NewThreadBuilder();
        $this->builder->setMessageClass($c['message']);
        $this->builder->setThreadClass($c['thread']);
        $this->builder->setMessageMetaClass($c['messageMeta']);
        $this->builder->setThreadMetaClass($c['threadMeta']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No sender set
     */
    public function testBuildWithNoSenderThrowsException()
    {
        //cannot use helper function because setting sender to null is against the argument requirement
        $this->builder->setCreatedAt($this->getDateOfNewThread());
        $this->builder->setBody(self::BODY);
        $this->builder->setIsSpam(false);
        $this->builder->setSubject(self::SUBJECT);
        $this->builder->setRecipients(array($this->recipient));
        $this->builder->build();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Body can't be empty
     */
    public function testBuildWithNoBodyThrowsException()
    {
        $this->builder = $this->getBuilderWithAllRequirementsSet();
        $this->builder->setBody('');
        $this->builder->build();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No date set
     */
    public function testBuildWithNoDateSetThrowsError()
    {
        $this->builder->setBody(self::BODY);
        $this->builder->setIsSpam(false);
        $this->builder->setSender($this->sender);
        $this->builder->setSubject(self::SUBJECT);
        $this->builder->setRecipients(array($this->recipient));
        $this->builder->build();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No subject set
     */
    public function testBuildWithNoSubjectSetThrowsError()
    {
        $this->builder = $this->getBuilderWithAllRequirementsSet();
        $this->builder->setSubject('');
        $this->builder->build();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage SetRecipients requires an array as argument
     */
    public function testSetRecipientsThrowsExceptionWhenNotArray()
    {
        $this->builder->setRecipients('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Recipients need to implement ParticipantInterface
     */
    public function testSetRecipientsThrowsExceptionWhenNotArrayWithParticipantInterfaces()
    {
        $this->builder->setRecipients(array('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage New thread requires at least one recipient
     */
    public function testBuildWithNoRecipientsThrowsException()
    {
        $this->builder->setCreatedAt($this->getDateOfNewThread());
        $this->builder->setBody(self::BODY);
        $this->builder->setIsSpam(false);
        $this->builder->setSender($this->sender);
        $this->builder->setSubject(self::SUBJECT);
        $this->builder->build();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The sender can not be a receiver
     */
    public function testSenderWhoIsAlsoReceiverThrowsException()
    {
        $this->builder = $this->getBuilderWithAllRequirementsSet();
        $this->builder->setSender($this->sender);
        $this->builder->setRecipients(array($this->sender, $this->recipient));
        $this->builder->build();
    }

    public function testBuilderFiltersDuplicateRecipientsWorksByCheckingAttribute()
    {
        $anotherRecipient = $this->getAnotherRecipient();
        $this->builder = $this->getBuilderWithAllRequirementsSet();
        $this->builder->setRecipients(array($this->recipient, $this->recipient, $anotherRecipient));
        $this->assertAttributeCount(3, 'recipients', $this->builder);
        $this->builder->build();
        $this->assertAttributeCount(2, 'recipients', $this->builder);
    }

    public function testBuilderFiltersDuplicateRecipientsWorskByCheckingParticipantCount()
    {
        $anotherRecipient = $this->getAnotherRecipient();
        $this->builder = $this->getBuilderWithAllRequirementsSet();
        $this->builder->setRecipients(array($this->recipient, $this->recipient, $anotherRecipient));
        $thread = $this->builder->build();
        $this->assertCount(2, $thread->getOtherParticipants($this->sender));
        $this->assertCount(3, $thread->getParticipants());
    }

    public function testSetRecipientsWithValidArgumentsWorks()
    {
        $this->builder->setRecipients(array($this->recipient));
        $this->assertAttributeContains($this->recipient, 'recipients', $this->builder);
    }

    public function testBuildWithAllRequirementsSetReturnsAThread()
    {
        $this->builder = $this->getBuilderWithAllRequirementsSet();
        $this->assertInstanceOf('FOS\MessageBundle\Model\ThreadInterface', $this->builder->build());
    }

    public function testANewThreadContainsOneMessage()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $this->assertCount(1, $thread->getMessages());
    }

    public function testThreadHasCorrectSubject()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $this->assertEquals(self::SUBJECT, $thread->getSubject());
    }

    public function testMessageHasCorrectSender()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $message = $this->getMessage($thread->getLastMessage());
        $this->assertEquals('sender_id', $message->getSender()->getId());
    }

    public function testMessageHasCorrectDate()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $message = $this->getMessage($thread->getLastMessage());
        $this->assertEquals($this->getDateOfNewThread(), $message->getCreatedAt());
    }

    public function testMessageHasCorrectBody()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $message = $this->getMessage($thread->getLastMessage());
        $this->assertEquals(self::BODY, $message->getBody());
    }

    public function testMessageIsReadBySender()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $message = $this->getMessage($thread->getLastMessage());
        $this->assertTrue($message->isReadByParticipant($this->sender));
    }

    public function testMessageIsNotReadByReceiver()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $message = $this->getMessage($thread->getLastMessage());
        $this->assertfalse($message->isReadByParticipant($this->recipient));
    }

    public function testThreadReturnsSettedSender()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $this->assertEquals('sender_id', $thread->getCreatedBy()->getId());
    }

    public function testSenderIsParticipient()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $this->assertTrue($thread->isParticipant($this->sender));
    }

    public function testRecipientIsParticipient()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $this->assertTrue($thread->isParticipant($this->recipient));
    }

    public function testThreadReturnsSettedReceiver()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $otherParticipants = $thread->getOtherParticipants($this->sender);
        foreach ($otherParticipants as $otherParticipant) {
            $this->assertEquals('receiver_id', $otherParticipant->getId());
        }
    }

    public function testThreadReturnsSettedDateCreated()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $this->assertEquals($this->getDateOfNewThread(), $thread->getCreatedAt());
    }

    public function testThreadReturnsSettedSpamValue()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $this->assertFalse($thread->getIsSpam());
    }

    /**
     * @return NewThreadBuilder with all requirements set
     */
    protected function getBuilderWithAllRequirementsSet()
    {
        $this->builder->setCreatedAt($this->getDateOfNewThread());
        $this->builder->setBody(self::BODY);
        $this->builder->setIsSpam(false);
        $this->builder->setSender($this->sender);
        $this->builder->setSubject(self::SUBJECT);
        $this->builder->setRecipients(array($this->recipient));

        return $this->builder;
    }
}
