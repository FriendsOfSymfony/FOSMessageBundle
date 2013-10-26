<?php

namespace FOS\MessageBundle\Tests\Twig;

use FOS\MessageBundle\Tests\Model\Thread;
use FOS\MessageBundle\Twig\MessageExtension;
use Mockery as m;

class MessageExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MessageExtension
     */
    private $extension;

    /**
     * @var \FOS\MessageBundle\Security\AuthorizerInterface|\Mockery\MockInterface
     */
    private $authorizer;

    /**
     * @var \FOS\MessageBundle\Security\ParticipantProviderInterface|\Mockery\MockInterface
     */
    private $participantProvider;

    /**
     * @var \FOS\MessageBundle\Provider\ProviderInterface|\Mockery\MockInterface
     */
    private $provider;

    public function testIsRead()
    {
        $this->participantProvider->shouldReceive('getAuthenticatedParticipant')
            ->andReturn($participant = $this->getParticipant());

        $readable = m::mock('FOS\\MessageBundle\\Model\\ReadableInterface');
        $readable->shouldReceive('isReadByParticipant')
            ->with($participant)
            ->andReturn(true);

        $this->extension->isRead($readable);
        $this->assertArrayHasKey('fos_message_is_read', $this->extension->getFunctions());
    }

    public function testNbRead()
    {
        $this->provider->shouldReceive('getNbUnreadMessages')
            ->andReturn(5)
            ->once();

        $this->assertEquals(5, $this->extension->getNbUnread());
        $this->assertEquals(5, $this->extension->getNbUnread());
        $this->assertArrayHasKey('fos_message_nb_unread', $this->extension->getFunctions());
    }

    public function testCanDeleteThread()
    {
        $thread = new Thread;

        $this->authorizer->shouldReceive('canDeleteThread')
            ->with($thread)
            ->andReturn(true)
            ->once();

        $this->assertTrue($this->extension->canDeleteThread($thread));
        $this->assertArrayHasKey('fos_message_can_delete_thread', $this->extension->getFunctions());
    }

    public function testIsThreadDeletedByParticipant()
    {
        $this->participantProvider->shouldReceive('getAuthenticatedParticipant')
            ->andReturn($participant = $this->getParticipant());

        $thread = m::mock('FOS\\MessageBundle\\Model\\ThreadInterface');
        $thread->shouldReceive('isDeletedByParticipant')
            ->with($participant)
            ->andReturn(false);

        $this->assertFalse($this->extension->isThreadDeletedByParticipant($thread));
        $this->assertArrayHasKey('fos_message_deleted_by_participant', $this->extension->getFunctions());
    }

    protected function setUp()
    {
        $this->authorizer = m::mock('FOS\\MessageBundle\\Security\\AuthorizerInterface');
        $this->participantProvider = m::mock('FOS\\MessageBundle\\Security\\ParticipantProviderInterface');
        $this->provider = m::mock('FOS\\MessageBundle\\Provider\\ProviderInterface');

        $this->extension = new MessageExtension($this->participantProvider, $this->provider, $this->authorizer);
    }

    /**
     * @return \FOS\MessageBundle\Model\ParticipantInterface|\Mockery\MockInterface
     */
    protected function getParticipant()
    {
        $participant = m::mock('FOS\\MessageBundle\\Model\\ParticipantInterface');

        return $participant;
    }
}
