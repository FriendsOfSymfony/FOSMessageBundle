<?php

namespace FOS\MessageBundle\Tests\Twig;

use FOS\MessageBundle\Security\BasicAuthorizer;
use FOS\MessageBundle\Security\ParticipantProvider;
use FOS\MessageBundle\Tests\Model\Thread;
use Mockery as m;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BasicAuthorizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BasicAuthorizer
     */
    private $authorizer;

    /**
     * @var \FOS\MessageBundle\Security\ParticipantProviderInterface|\Mockery\MockInterface
     */
    private $participantProvider;

    /**
     * @var \FOS\MessageBundle\Model\ParticipantInterface|\Mockery\MockInterface
     */
    private $participant;

    public function testCanSeeThread()
    {
        $thread = new Thread;
        $thread->addParticipant($this->participant);

        $canSee = $this->authorizer->canSeeThread($thread);

        $this->assertTrue($canSee);
    }

    public function testCantSeeThread()
    {
        $thread = new Thread;

        $canSee = $this->authorizer->canSeeThread($thread);

        $this->assertFalse($canSee);
    }

    public function testCanDeleteThread()
    {
        $thread = new Thread;
        $thread->addParticipant($this->participant);

        $canDelete = $this->authorizer->canDeleteThread($thread);

        $this->assertTrue($canDelete);
    }

    public function testCantDeleteThread()
    {
        $thread = new Thread;

        $canDelete = $this->authorizer->canDeleteThread($thread);

        $this->assertFalse($canDelete);
    }

    public function testCanMessageParticipant()
    {
        $canMessage = $this->authorizer->canMessageParticipant($this->participant);

        $this->assertTrue($canMessage);
    }

    protected function setUp()
    {
        $this->participant = m::mock('FOS\\MessageBundle\\Model\\ParticipantInterface');

        $this->participantProvider = m::mock('FOS\\MessageBundle\\Security\\ParticipantProviderInterface');
        $this->participantProvider->shouldReceive('getAuthenticatedParticipant')
            ->andReturn($this->participant);

        $this->authorizer = new BasicAuthorizer($this->participantProvider);
    }
}
