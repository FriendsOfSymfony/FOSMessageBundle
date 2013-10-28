<?php

namespace FOS\MessageBundle\Tests\Deleter;

use FOS\MessageBundle\Deleter\Deleter;
use FOS\MessageBundle\Event\FOSMessageEvents;
use FOS\MessageBundle\Tests\Model\Thread;
use Mockery as m;

class DeleterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Deleter
     */
    private $deleter;

    /**
     * @var \FOS\MessageBundle\Security\AuthorizerInterface|\Mockery\MockInterface
     */
    private $authorizer;

    /**
     * @var \FOS\MessageBundle\Model\ParticipantInterface|\Mockery\MockInterface
     */
    private $participant;

    /**
     * @var \FOS\MessageBundle\Security\ParticipantProviderInterface|\Mockery\MockInterface
     */
    private $participantProvider;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\Mockery\MockInterface
     */
    private $dispatcher;

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testMarkAsDeletedNotAllowed()
    {
        $this->authorizer->shouldReceive('canDeleteThread')
            ->andReturn(false);

        $thread = new Thread;

        $this->deleter->markAsDeleted($thread);
    }

    public function testMarkAsDeleted()
    {
        $this->authorizer->shouldReceive('canDeleteThread')
            ->andReturn(true);

        $thread = m::mock('FOS\MessageBundle\Model\ThreadInterface');
        $thread->shouldReceive('setIsDeletedByParticipant')
            ->with($this->participant, true);

        $this->dispatcher->shouldReceive('dispatch')
            ->with(FOSMessageEvents::POST_DELETE, m::type('FOS\MessageBundle\Event\ThreadEvent'));

        $this->deleter->markAsDeleted($thread);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testMarkAsUnDeletedNotAllowed()
    {
        $this->authorizer->shouldReceive('canDeleteThread')
            ->andReturn(false);

        $thread = new Thread;

        $this->deleter->markAsUndeleted($thread);
    }

    public function testMarkAsUnDeleted()
    {
        $this->authorizer->shouldReceive('canDeleteThread')
            ->andReturn(true);

        $thread = m::mock('FOS\MessageBundle\Model\ThreadInterface');
        $thread->shouldReceive('setIsDeletedByParticipant')
            ->with($this->participant, false);

        $this->dispatcher->shouldReceive('dispatch')
            ->with(FOSMessageEvents::POST_UNDELETE, m::type('FOS\MessageBundle\Event\ThreadEvent'));

        $this->deleter->markAsUndeleted($thread);
    }

    public function setUp()
    {
        $this->participant = m::mock('FOS\MessageBundle\Model\ParticipantInterface');
        $this->participantProvider = m::mock('FOS\MessageBundle\Security\ParticipantProviderInterface');
        $this->participantProvider->shouldReceive('getAuthenticatedParticipant')
            ->andReturn($this->participant);

        $this->authorizer = m::mock('FOS\MessageBundle\Security\AuthorizerInterface');
        $this->dispatcher = m::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->deleter = new Deleter($this->authorizer, $this->participantProvider, $this->dispatcher);
    }
}
