<?php

namespace FOS\MessageBundle\Security;

use FOS\MessageBundle\Model\ParticipantInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Provides the authenticated participant.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ParticipantProvider implements ParticipantProviderInterface
{
    /**
     * @var SecurityContextInterface|TokenStorageInterface
     */
    protected $securityContext;

    public function __construct($securityContext)
    {
        if (!$securityContext instanceof SecurityContextInterface && !$securityContext instanceof TokenStorageInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Argument 1 passed to ParticipantProvider::__construct is not valid (instance of %s or %s expected, %s given)',
                'Symfony\Component\Security\Core\SecurityContextInterface',
                'Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface',
                is_object($securityContext) ? get_class($securityContext) : gettype($securityContext)
            ));
        }

        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
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
