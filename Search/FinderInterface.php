<?php

namespace FOS\MessageBundle\Search;

/**
 * Finds threads of a participant, matching a given query
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface FinderInterface
{
    /**
     * Finds threads of a participant, matching a given query
     *
     * @param  Query $query
     * @return array of ThreadInterface
     */
    public function find(Query $query);

    /**
     * Finds threads of a participant, matching a given query
     *
     * @param  Query   $query
     * @return Builder a query builder suitable for pagination
     */
    public function getQueryBuilder(Query $query);
}
