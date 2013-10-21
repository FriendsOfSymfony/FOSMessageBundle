<?php

namespace FOS\MessageBundle\Tests\MessageBuilder\Orm;

use FOS\MessageBundle\MessageBuilder\Orm\ReplyBuilder;
use FOS\MessageBundle\Model\ParticipantInterface;

/**
 * Test for the replybuilder
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class ReplyBuilderTest extends OrmBuilderTestHelpers
{
    /**
     * The reply builder
     *
     * @var ReplyBuilder
     */
    protected $replyBuilder;

    /**
     * The date of the reply
     *
     * @var \DateTime
     */
    protected $DateReply;

    /**
     * The sender
     *
     * @var ParticipantInterface
     */
    protected $sender;

    /**
     * The recipient
     *
     * @var ParticipantInterface
     */
    protected $recipient;

    /**
     * The body of the message
     */
    const BODY = "this is a reply";

    public function setUp()
    {
        $this->sender = $this->getSender();
        $this->recipient = $this->getRecipient();
        $this->dateReply = new \DateTime('2013-10-24');

        $c = $this->getClasses();
        $this->replyBuilder = new ReplyBuilder();
        $this->replyBuilder->setMessageClass($c['message']);
        $this->replyBuilder->setThreadClass($c['thread']);
        $this->replyBuilder->setMessageMetaClass($c['messageMeta']);
        $this->replyBuilder->setThreadMetaClass($c['threadMeta']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No sender set
     */
    public function testBuildWithNoSenderThrowsError()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $this->replyBuilder->setBody('this is a reply');
        $this->replyBuilder->setCreatedAt($this->dateReply);
        $this->replyBuilder->setThread($thread);
        $this->replyBuilder->build();
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @expectedExceptionMessage You are no participant of this thread
     */
    public function testBuildWithSenderNotParticipantOfThreadThrowsException()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $this->replyBuilder->setBody('this is a reply');
        $this->replyBuilder->setCreatedAt($this->dateReply);
        $this->replyBuilder->setThread($thread);
        $this->replyBuilder->setSender($this->getAnotherRecipient());
        $this->replyBuilder->build();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No date set
     */
    public function testBuildWithNoDateSetThrowsError()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $this->replyBuilder->setBody('this is a reply');
        $this->replyBuilder->setThread($thread);
        $this->replyBuilder->setSender($this->sender);
        $this->replyBuilder->build();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Body can't be empty
     */
    public function testBuildWithNoBodyThrowsError()
    {
        $this->replyBuilder = $this->expectsAllRequirementsSet();
        $this->replyBuilder->setBody('');
        $this->replyBuilder->build();
    }

    public function testBuilderReturnsAThread()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $this->replyBuilder->setBody('this is a reply');
        $this->replyBuilder->setCreatedAt($this->dateReply);
        $this->replyBuilder->setThread($thread);
        $this->replyBuilder->setSender($this->sender);
        $this->assertInstanceOf('FOS\MessageBundle\Model\ThreadInterface', $this->replyBuilder->build());
    }

    public function testBuilderWithOneReplyContains2Messages()
    {
        $threadWeReplyTo = $this->getNewThread($this->sender, $this->recipient);
        $this->replyBuilder->setBody('this is a reply');
        $this->replyBuilder->setCreatedAt($this->dateReply);
        $this->replyBuilder->setThread($threadWeReplyTo);
        $this->replyBuilder->setSender($this->sender);
        $thread = $this->replyBuilder->build();

        $this->assertEquals(2, count($thread->getMessages()));
    }

    public function testBuilderWith2NewRepliesContains3Messages()
    {
        $threadWeReplyTo = $this->getNewThread($this->sender, $this->recipient);

        //first reply
        $this->replyBuilder->setBody('this is a reply');
        $this->replyBuilder->setCreatedAt($this->dateReply);
        $this->replyBuilder->setThread($threadWeReplyTo);
        $this->replyBuilder->setSender($this->sender);
        $threadOneReply = $this->replyBuilder->build();

        //second reply
        $this->replyBuilder->setBody('this is a reply');
        $this->replyBuilder->setCreatedAt($this->dateReply);
        $this->replyBuilder->setThread($threadOneReply);
        $this->replyBuilder->setSender($this->sender);
        $thread = $this->replyBuilder->build();

        $this->assertEquals(3, count($thread->getMessages()));
    }

    public function testSenderWhoRepliesHasSeenHisReplyMessage()
    {
        $threadWithReply = $this->getThreadWithReply();
        $lastMessage = $this->getMessage($threadWithReply->getLastMessage());
        $this->assertTrue($lastMessage->isReadByParticipant($this->sender));
    }

    public function testRecipientHasNotSeenReplyMessage()
    {
        $threadWithReply = $this->getThreadWithReply();
        $lastMessage = $this->getMessage($threadWithReply->getLastMessage());
        $this->assertFalse($lastMessage->isReadByParticipant($this->recipient));
    }

    public function testReplyMessageHasTheRightDate()
    {
        $threadWithReply = $this->getThreadWithReply();
        $this->assertEquals($this->dateReply, $threadWithReply->getLastMessage()->getCreatedAt());
    }

    public function testSenderOfReplyIsSenderOfLastMessage()
    {
        $threadWithReply = $this->getThreadWithReply();
        $lastMessage = $this->getMessage($threadWithReply->getLastMessage());
        $this->assertEquals($this->sender, $lastMessage->getSender());
    }

    public function testThreadIsNoLongerDeletedForSenderWhenNewReply()
    {
        $threadWithReply = $this->getThreadWithReply();
        $threadWithReply->setIsDeletedByParticipant($this->sender, true);
        $this->assertTrue($threadWithReply->isDeletedByParticipant($this->sender));

        //second reply
        $this->replyBuilder->setBody('this is a reply');
        $this->replyBuilder->setCreatedAt($this->dateReply);
        $this->replyBuilder->setThread($threadWithReply);
        $this->replyBuilder->setSender($this->sender);
        $thread = $this->replyBuilder->build();

        $this->assertFalse($thread->isDeletedByParticipant($this->sender));
    }

    public function testThreadIsNoLongerDeletedForRecipientWhenNewReply()
    {
        $threadWithReply = $this->getThreadWithReply();
        $threadWithReply->setIsDeletedByParticipant($this->recipient, true);

        //second reply
        $this->replyBuilder->setBody('this is a reply');
        $this->replyBuilder->setCreatedAt($this->dateReply);
        $this->replyBuilder->setThread($threadWithReply);
        $this->replyBuilder->setSender($this->sender);
        $thread = $this->replyBuilder->build();

        $this->assertFalse($thread->isDeletedByParticipant($this->recipient));
    }

    /**
     * If there is a last participant message date set for the sender it comes in the outbox
     */
    public function testNewReplyUpdatesLastParticipantMessageDateForSender()
    {
        $thread = $this->getThreadWithReply();
        $threadMeta = $thread->getMetadataForParticipant($this->sender);
        $this->assertEquals($this->dateReply, $threadMeta->getLastParticipantMessageDate());
    }

    /**
     * If there is a last participant message date it comes in the outbox. We don't want
     * this to happen when we receive a new message
     */
    public function testNewReplyDoesNotUpdateLastParticipantMessageDateForRecipient()
    {
        $thread = $this->getThreadWithReply();

        //we let the recipient reply, here the sender should have had his lastmessagedate updated
        $replyDateRecipient = $this->getDateOfNewThread()->modify('+30 minutes');
        $this->replyBuilder->setThread($thread);
        $this->replyBuilder->setBody('this is a reply');
        $this->replyBuilder->setCreatedAt($replyDateRecipient);
        $this->replyBuilder->setSender($this->recipient);
        $threadReplyRecipient = $this->replyBuilder->build();
        //the date of last message becomes the reply date now
        //now the sender replies again, this shouldn't update the last message date
        $this->replyBuilder->setBody('this is a reply');
        $replyDateSender = $this->getDateOfNewThread()->modify('+60 minutes');
        $this->replyBuilder->setCreatedAt($replyDateSender);
        $this->replyBuilder->setThread($threadReplyRecipient);
        $this->replyBuilder->setSender($this->sender);
        $threadSecondReplySender = $this->replyBuilder->build();

        $threadMeta = $threadSecondReplySender->getMetadataForParticipant($this->recipient);

        $this->assertEquals($replyDateRecipient, $threadMeta->getLastParticipantMessageDate());
    }

    /**
     * The inbox is sorted by the last message date so we don't want to update that if
     * the sender sends a new message
     */
    public function testNewReplyDoesNotUpdateLastMessageDateForSender()
    {
        $thread = $this->getThreadWithReply();

        //we let the recipient reply, here the sender should have had his lastmessagedate updated
        $replyDateRecipient = $this->getDateOfNewThread()->modify('+30 minutes');
        $this->replyBuilder->setThread($thread);
        $this->replyBuilder->setBody('this is a reply');
        $this->replyBuilder->setCreatedAt($replyDateRecipient);
        $this->replyBuilder->setSender($this->recipient);
        $threadReplyRecipient = $this->replyBuilder->build();
        //the date of last message becomes the reply date now

        //now the sender replies again, this shouldn't update the last message date
        $this->replyBuilder->setBody('this is a reply');
        $replyDateSender = $this->getDateOfNewThread()->modify('+60 minutes');
        $this->replyBuilder->setCreatedAt($replyDateSender);
        $this->replyBuilder->setThread($threadReplyRecipient);
        $this->replyBuilder->setSender($this->sender);
        $threadSecondReplySender = $this->replyBuilder->build();

        $threadMeta = $threadSecondReplySender->getMetadataForParticipant($this->sender);

        $this->assertEquals($replyDateRecipient, $threadMeta->getLastMessageDate());
    }

    /**
     * The recipient receives a new message so we want it in the inbox
     *
     * This requires a last message date set for the thread that is not from the participant
     */
    public function testNewReplyDoesUpdateLastMessageDateForRecipient()
    {
        $thread = $this->getThreadWithReply();
        $threadMeta = $thread->getMetadataForParticipant($this->recipient);
        $this->assertEquals($this->dateReply, $threadMeta->getLastMessageDate());
    }

    public function testReplyMessageHasTheRightBody()
    {
        $threadWithReply = $this->getThreadWithReply();
        $this->assertEquals(self::BODY, $threadWithReply->getLastMessage()->getBody());
    }

    /**
     * Helper function so we don't always have to build a new reply in every test
     */
    protected function getThreadWithReply()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $this->replyBuilder->setBody(self::BODY);
        $this->replyBuilder->setCreatedAt($this->dateReply);
        $this->replyBuilder->setThread($thread);
        $this->replyBuilder->setSender($this->sender);

        return $this->replyBuilder->build();
    }

    protected function expectsAllRequirementsSet()
    {
        $thread = $this->getNewThread($this->sender, $this->recipient);
        $this->replyBuilder->setBody('this is a reply');
        $this->replyBuilder->setCreatedAt($this->dateReply);
        $this->replyBuilder->setThread($thread);
        $this->replyBuilder->setSender($this->sender);

        return $this->replyBuilder;
    }
}
