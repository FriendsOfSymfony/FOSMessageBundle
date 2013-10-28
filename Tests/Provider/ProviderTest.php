<?php

namespace FOS\MessageBundle\Tests\Deleter;

use FOS\MessageBundle\Provider\Provider;
use FOS\MessageBundle\Tests\Model\Thread;
use Mockery as m;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Provider
     */
    private $provider;

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
     * @var \FOS\MessageBundle\ModelManager\MessageManagerInterface|\Mockery\MockInterface
     */
    private $messageManager;

    /**
     * @var \FOS\MessageBundle\ModelManager\ThreadManagerInterface|\Mockery\MockInterface
     */
    private $threadManager;

    /**
     * @var \FOS\MessageBundle\Reader\ReaderInterface|\Mockery\MockInterface
     */
    private $threadReader;

    public function testGetInboxThreads()
    {
        $this->threadManager->shouldReceive('findParticipantInboxThreads')
            ->with($this->participant)
            ->andReturn(array());

        $threads = $this->provider->getInboxThreads();

        $this->assertCount(0, $threads);
    }

    public function testGetSentThreads()
    {
        $this->threadManager->shouldReceive('findParticipantSentThreads')
            ->with($this->participant)
            ->andReturn(array());

        $threads = $this->provider->getSentThreads();

        $this->assertCount(0, $threads);
    }

    public function testGetDeletedThreads()
    {
        $this->threadManager->shouldReceive('findParticipantDeletedThreads')
            ->with($this->participant)
            ->andReturn(array());

        $threads = $this->provider->getDeletedThreads();

        $this->assertCount(0, $threads);
    }

    public function testGetNbUnreadMessageByParticipant()
    {
        $this->messageManager->shouldReceive('getNbUnreadMessageByParticipant')
            ->with($this->participant)
            ->andReturn(4);

        $unreadMessages = $this->provider->getNbUnreadMessages();

        $this->assertEquals(4, $unreadMessages);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetThread404()
    {
        $this->threadManager->shouldReceive('findThreadById')
            ->with(2)
            ->andReturnNull();

        $this->provider->getThread(2);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testGetThread403()
    {
        $thread = new Thread;

        $this->threadManager->shouldReceive('findThreadById')
            ->with(2)
            ->andReturn($thread);
        $this->authorizer->shouldReceive('canSeeThread')
            ->with($thread)
            ->andReturn(false);

        $this->provider->getThread(2);
    }

    public function testGetThread()
    {
        $thread = new Thread;

        $this->threadManager->shouldReceive('findThreadById')
            ->with(2)
            ->andReturn($thread);
        $this->authorizer->shouldReceive('canSeeThread')
            ->with($thread)
            ->andReturn(true);
        $this->threadReader->shouldReceive('markAsRead')
            ->with($thread);

        $result = $this->provider->getThread(2);

        $this->assertSame($thread, $result);
    }

/*
$thread = $this->threadManager->findThreadById($threadId);
if (!$thread) {
throw new NotFoundHttpException('There is no such thread');
}
if (!$this->authorizer->canSeeThread($thread)) {
    throw new AccessDeniedException('You are not allowed to see this thread');
}
// Load the thread messages before marking them as read
// because we want to see the unread messages
$thread->getMessages();
$this->threadReader->markAsRead($thread);

return $thread;*/

    public function setUp()
    {
        $this->participant = m::mock('FOS\MessageBundle\Model\ParticipantInterface');
        $this->participantProvider = m::mock('FOS\MessageBundle\Security\ParticipantProviderInterface');
        $this->participantProvider->shouldReceive('getAuthenticatedParticipant')
            ->andReturn($this->participant);

        $this->authorizer = m::mock('FOS\MessageBundle\Security\AuthorizerInterface');
        $this->messageManager = m::mock('FOS\MessageBundle\ModelManager\MessageManagerInterface');
        $this->threadManager = m::mock('FOS\MessageBundle\ModelManager\ThreadManagerInterface');
        $this->threadReader = m::mock('FOS\MessageBundle\Reader\ReaderInterface');

        $this->provider = new Provider(
            $this->threadManager,
            $this->messageManager,
            $this->threadReader,
            $this->authorizer,
            $this->participantProvider
        );
    }
}
