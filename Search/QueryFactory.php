<?php

namespace FOS\MessageBundle\Search;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Gets the search term from the request and prepares it.
 */
class QueryFactory implements QueryFactoryInterface
{
    protected $request;

    /**
     * The query parameter containing the search term.
     *
     * @var string
     */
    protected $queryParameter;

    /**
     * Instanciates a new TermGetter.
     *
     * @param RequestStack|Request $requestStack
     * @param string               $queryParameter
     */
    public function __construct($requestStack, $queryParameter)
    {
        $this->request = $requestStack;
        $this->queryParameter = $queryParameter;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromRequest()
    {
        $original = $this->getCurrentRequest()->query->get($this->queryParameter);
        $original = trim($original);

        $escaped = $this->escapeTerm($original);

        return new Query($original, $escaped);
    }

    /**
     * Sets: the query parameter containing the search term.
     *
     * @param string $queryParameter
     */
    public function setQueryParameter($queryParameter)
    {
        $this->queryParameter = $queryParameter;
    }

    protected function escapeTerm($term)
    {
        return $term;
    }

    /**
     * BC layer to retrieve the current request directly or from a stack.
     *
     * @return null|Request
     */
    private function getCurrentRequest()
    {
        if ($this->request instanceof Request) {
            return $this->request;
        }

        return $this->request->getCurrentRequest();
    }
}
