<?php

namespace FOS\MessageBundle\Search;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Gets the search term from the request and prepares it
 */
class QueryFactory implements QueryFactoryInterface
{
    /**
     * @var Request
     */
    protected $request = null;

    /**
     * the query parameter containing the search term
     *
     * @var string
     */
    protected $queryParameter;

    /**
     * Instanciates a new TermGetter
     *
     * @param RequestStack $requestStack
     * @param string $queryParameter
     */
    public function __construct(RequestStack $requestStack, $queryParameter)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->queryParameter = $queryParameter;
    }

    /**
     * Gets the search term
     *
     * @return Term the term object
     */
    public function createFromRequest()
    {
        $original = $this->request->query->get($this->queryParameter);
        $original = trim($original);

        $escaped = $this->escapeTerm($original);

        return new Query($original, $escaped);
    }

    /**
     * Sets: the query parameter containing the search term
     *
     * @param string queryParameter
     */
    public function setQueryParameter($queryParameter)
    {
        $this->queryParameter = $queryParameter;
    }

    protected function escapeTerm($term)
    {
        return $term;
    }
}
