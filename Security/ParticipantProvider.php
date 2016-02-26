<?php

namespace FOS\MessageBundle\Security;

use FOS\MessageBundle\Model\ParticipantInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Provides the authenticated participant
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ParticipantProvider implements ParticipantProviderInterface
{
    /**
     * The security context
     *
     * @var SecurityContextInterface
     */
    protected $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * Gets the current authenticated user
     *
     * @return ParticipantInterface
     */
    public function getAuthenticatedParticipant()
    {
        $participant = $this->securityContext->getToken()->getUser();

        if (!$participant instanceof ParticipantInterface) {
            throw new AccessDeniedException('Must be logged in with a ParticipantInterface instance');
        }

        return $participant;
    }
}
