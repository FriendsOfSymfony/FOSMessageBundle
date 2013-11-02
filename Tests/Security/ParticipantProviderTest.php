<?php

namespace FOS\MessageBundle\Tests\Twig;

use FOS\MessageBundle\Security\ParticipantProvider;
use Mockery as m;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ParticipantProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ParticipantProvider
     */
    private $provider;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface|\Mockery\MockInterface
     */
    private $securityContext;

    public function testGetParticipant()
    {
        $user = m::mock('FOS\\MessageBundle\\Model\\ParticipantInterface, Symfony\\Component\\Security\\Core\\User\\UserInterface');
        $user->shouldReceive('getRoles')
            ->andReturn(array());

        $this->securityContext->shouldReceive('getToken')
            ->andReturn(new RememberMeToken($user, 'providerKey', 'key'));

        $this->provider->getAuthenticatedParticipant();
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testGetInvalidParticipant()
    {
        $user = m::mock('Symfony\\Component\\Security\\Core\\User\\UserInterface');
        $user->shouldReceive('getRoles')
            ->andReturn(array());

        $this->securityContext->shouldReceive('getToken')
            ->andReturn(new RememberMeToken($user, 'providerKey', 'key'));

        $this->provider->getAuthenticatedParticipant();
    }

    protected function setUp()
    {
        $this->securityContext = m::mock('Symfony\\Component\\Security\\Core\\SecurityContextInterface');
        $this->provider = new ParticipantProvider($this->securityContext);
    }
}
