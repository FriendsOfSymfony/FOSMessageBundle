<?php

namespace FOS\MessageBundle\Tests\Twig;

use FOS\MessageBundle\Twig\MessageExtension;
use Mockery as m;

class MessageExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MessageExtension
     */
    private $extension;

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
    }

    protected function setUp()
    {
        $this->participantProvider = m::mock('FOS\\MessageBundle\\Security\\ParticipantProviderInterface');
        $this->provider = m::mock('FOS\\MessageBundle\\Provider\\ProviderInterface');

        $this->extension = new MessageExtension($this->participantProvider, $this->provider);
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
