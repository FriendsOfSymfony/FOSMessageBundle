<?php

namespace Ornicar\MessageBundle\Document;

use DateTime;

class ThreadDenormalizationTest extends \PHPUnit_Framework_TestCase
{
    protected $dates;

    protected function setUp()
    {
        $this->dates = array(
            new DateTime('- 3 days'),
            new DateTime('- 2 days'),
            new DateTime('- 1 days'),
            new DateTime('- 1 hour')
        );
    }

    public function testDenormalize()
    {
        $thread = new TestThread();
        $user1 = $this->createUserMock('u1');
        $user2 = $this->createUserMock('u2');

        /**
         * First message
         */
        $message = $this->createMessageMock($user1, $user2, $this->dates[0]);
        $thread->addMessage($message);

        $this->assertSame(array($user1, $user2), $thread->getParticipants());
        $this->assertSame(array('u2' => $this->dates[0]->getTimestamp()), $thread->getDatesOfLastMessageWrittenByOtherUser());
        $this->assertSame(array('u1' => $this->dates[0]->getTimestamp()), $thread->getDatesOfLastMessageWrittenByUser());

        /**
         * Second message
         */
        $message = $this->createMessageMock($user2, $user1, $this->dates[1]);
        $thread->addMessage($message);

        $this->assertSame(array($user1, $user2), $thread->getParticipants());
        $this->assertSame(array('u1' => $this->dates[1]->getTimestamp(), 'u2' => $this->dates[0]->getTimestamp()), $thread->getDatesOfLastMessageWrittenByOtherUser());
        $this->assertSame(array('u1' => $this->dates[0]->getTimestamp(), 'u2' => $this->dates[1]->getTimestamp()), $thread->getDatesOfLastMessageWrittenByUser());

        /**
         * Third message
         */
        $message = $this->createMessageMock($user2, $user1, $this->dates[2]);
        $thread->addMessage($message);

        $this->assertSame(array($user1, $user2), $thread->getParticipants());
        $this->assertSame(array('u1' => $this->dates[2]->getTimestamp(), 'u2' => $this->dates[0]->getTimestamp()), $thread->getDatesOfLastMessageWrittenByOtherUser());
        $this->assertSame(array('u1' => $this->dates[0]->getTimestamp(), 'u2' => $this->dates[2]->getTimestamp()), $thread->getDatesOfLastMessageWrittenByUser());

        /**
         * Fourth message
         */
        $message = $this->createMessageMock($user1, $user2, $this->dates[3]);
        $thread->addMessage($message);

        $this->assertSame(array($user1, $user2), $thread->getParticipants());
        $this->assertSame(array('u1' => $this->dates[2]->getTimestamp(), 'u2' => $this->dates[3]->getTimestamp()), $thread->getDatesOfLastMessageWrittenByOtherUser());
        $this->assertSame(array('u1' => $this->dates[3]->getTimestamp(), 'u2' => $this->dates[2]->getTimestamp()), $thread->getDatesOfLastMessageWrittenByUser());

    }

    protected function createMessageMock($sender, $recipient, DateTime $date)
    {
        $message = $this->getMockBuilder('Ornicar\MessageBundle\Model\MessageInterface')
            ->disableOriginalConstructor(true)
            ->getMock();

        $message->expects($this->once())
            ->method('getSender')
            ->will($this->returnValue($sender));
        $message->expects($this->once())
            ->method('getRecipient')
            ->will($this->returnValue($recipient));
        $message->expects($this->once())
            ->method('getCreatedAt')
            ->will($this->returnValue($date));

        return $message;
    }

    protected function createUserMock($id)
    {
        $user = $this->getMockBuilder('Application\UserBundle\Document\User')
            ->disableOriginalConstructor(true)
            ->getMock();

        $user->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

        return $user;
    }
}

class TestThread extends Thread
{
    public function getDatesOfLastMessageWrittenByUser()
    {
        return $this->datesOfLastMessageWrittenByUser;
    }

    public function getDatesOfLastMessageWrittenByOtherUser()
    {
        return $this->datesOfLastMessageWrittenByOtherUser;
    }
}
