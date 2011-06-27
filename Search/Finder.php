<?php

namespace Ornicar\MessageBundle\Search;

use Ornicar\MessageBundle\ModelManager\ThreadManagerInterface;
use Ornicar\MessageBundle\Authorizer\AuthorizerInterface;

/**
 * Finds threads of a participant, matching a given query
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Finder implements FinderInterface
{
    /**
     * The authorizer instance
     *
     * @var AuthorizerInterface
     */
    protected $authorizer;

    /**
     * The thread manager
     *
     * @var ThreadManagerInterface
     */
    protected $threadManager;

    public function __construct(AuthorizerInterface $authorizer, ThreadManagerInterface $threadManager)
    {
        $this->authorizer = $authorizer;
        $this->threadManager = $threadManager;
    }

    /**
     * Finds threads of a participant, matching a given query
     *
     * @param Query $query
     * @return array of ThreadInterface
     */
    public function find(Query $query)
    {
        return $this->threadManager->findParticipantThreadsBySearch($this->getAuthenticatedParticipant(), $query->getEscaped());
    }

    /**
     * Finds threads of a participant, matching a given query
     *
     * @param Query $query
     * @return mixed a query builder suitable for pagination
     */
    public function getQueryBuilder(Query $query)
    {
        return $this->threadManager->getParticipantThreadsBySearchQueryBuilder($this->getAuthenticatedParticipant(), $query->getEscaped());
    }

    /**
     * Gets the current authenticated user
     *
     * @return ParticipantInterface
     */
    protected function getAuthenticatedParticipant()
    {
        return $this->authorizer->getAuthenticatedParticipant();
    }
}
