<?php

namespace FOS\MessageBundle\Tests\Twig;

use FOS\MessageBundle\Event\FOSMessageEvents;
use FOS\MessageBundle\Sender\Sender;
use FOS\MessageBundle\Tests\Model\Message;
use FOS\MessageBundle\Tests\Model\Thread;
use Mockery as m;

class SenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var \FOS\MessageBundle\ModelManager\MessageManagerInterface|\Mockery\MockInterface
     */
    private $messageManager;

    /**
     * @var \FOS\MessageBundle\ModelManager\ThreadManagerInterface|\Mockery\MockInterface
     */
    private $threadManager;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\Mockery\MockInterface
     */
    private $eventDispatcher;

    public function testSend()
    {
        $thread = new Thread;
        $message = new Message;
        $thread->addMessage($message);

        $this->threadManager->shouldReceive('saveThread')
            ->with($thread, false);
        $this->messageManager->shouldReceive('saveMessage')
            ->with($message, false);
        $this->messageManager->shouldReceive('saveMessage')
            ->with($message);

        $that = $this;
        $this->eventDispatcher->shouldReceive('dispatch')
            ->with(FOSMessageEvents::POST_SEND, m::on(function ($event) use ($message, $that) {
                $that->assertInstanceOf('FOS\\MessageBundle\\Event\\MessageEvent', $event);
                $that->assertEquals($message, $event->getMessage());

                return true;
            }));

        $this->sender->send($message);
    }

    protected function setUp()
    {
        $this->messageManager = m::mock('FOS\\MessageBundle\\ModelManager\\MessageManagerInterface');
        $this->threadManager = m::mock('FOS\\MessageBundle\\ModelManager\\ThreadManager');
        $this->eventDispatcher = m::mock('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface');

        $this->sender = new Sender($this->messageManager, $this->threadManager, $this->eventDispatcher);
    }
}
