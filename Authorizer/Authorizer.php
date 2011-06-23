<?php

namespace Ornicar\MessageBundle\Authorizer;

use Ornicar\MessageBundle\Model\ThreadInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class Authorizer implements AuthorizerInterface
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
     * Tells if the current user is allowed
     * to see this thread
     *
     * @param ThreadInterface $thread
     * @return boolean
     */
    public function canSeeThread(ThreadInterface $thread)
    {
        return $this->isAuthenticated() && $thread->isParticipant($this->getAuthenticatedUser());
    }

    /**
     * Gets the current authenticated user
     *
     * @return UserInterface
     */
    protected function getAuthenticatedUser()
    {
        return $this->securityContext->getToken()->getUser();
    }

    /**
     * Tells if there is an authenticated user
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }
}
