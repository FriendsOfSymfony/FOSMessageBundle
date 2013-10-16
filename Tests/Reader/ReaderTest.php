<?php

namespace FOS\MessageBundle\Tests\Reader;

use FOS\MessageBundle\Event\FOSMessageEvents;
use FOS\MessageBundle\Event\ReadableEvent;
use FOS\MessageBundle\Reader\Reader;
use Mockery as m;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var \FOS\MessageBundle\Model\ParticipantInterface
     */
    private $participant;

    /**
     * @var \FOS\MessageBundle\Security\ParticipantProviderInterface|\Mockery\MockInterface
     */
    private $participantProvider;

    /**
     * @var \FOS\MessageBundle\ModelManager\ReadableManagerInterface|\Mockery\MockInterface
     */
    private $readableManager;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\Mockery\MockInterface
     */
    private $dispatcher;

    public function testMarkAsReadAlreadyRead()
    {
        $readable = m::mock('FOS\\MessageBundle\\Model\\ReadableInterface');
        $readable->shouldReceive('isReadByParticipant')
            ->with($this->participant)
            ->andReturn(true);

        $this->reader->markAsRead($readable);
    }

    public function testMarkAsUnreadAlreadyUnread()
    {
        $readable = m::mock('FOS\\MessageBundle\\Model\\ReadableInterface');
        $readable->shouldReceive('isReadByParticipant')
            ->with($this->participant)
            ->andReturn(false);

        $this->reader->markAsUnread($readable);
    }

    public function testMarkAsRead()
    {
        $readable = m::mock('FOS\\MessageBundle\\Model\\ReadableInterface');
        $readable->shouldReceive('isReadByParticipant')
            ->with($this->participant)
            ->andReturn(false);

        $this->readableManager->shouldReceive('markAsReadByParticipant')
            ->with($readable, $this->participant);
        $this->dispatcher->shouldReceive('dispatch')
            ->with(FOSMessageEvents::POST_READ, m::on(function (ReadableEvent $event) use ($readable) {
                $this->assertSame($readable, $event->getReadable());

                return true;
            }));

        $this->reader->markAsRead($readable);
    }

    public function testMarkAsUnread()
    {
        $readable = m::mock('FOS\\MessageBundle\\Model\\ReadableInterface');
        $readable->shouldReceive('isReadByParticipant')
            ->with($this->participant)
            ->andReturn(true);

        $this->readableManager->shouldReceive('markAsUnreadByParticipant')
            ->with($readable, $this->participant);
        $this->dispatcher->shouldReceive('dispatch')
            ->with(FOSMessageEvents::POST_UNREAD, m::on(function (ReadableEvent $event) use ($readable) {
                $this->assertSame($readable, $event->getReadable());

                return true;
            }));

        $this->reader->markAsUnread($readable);
    }

    protected function setUp()
    {
        $this->participant = m::mock('FOS\\MessageBundle\\Model\\ParticipantInterface');
        $this->participantProvider = m::mock('FOS\\MessageBundle\\Security\\ParticipantProviderInterface');
        $this->participantProvider->shouldReceive('getAuthenticatedParticipant')
            ->andReturn($this->participant);

        $this->readableManager = m::mock('FOS\\MessageBundle\\ModelManager\\ReadableManagerInterface');
        $this->dispatcher = m::mock('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface');

        $this->reader = new Reader($this->participantProvider, $this->readableManager, $this->dispatcher);
    }
}
