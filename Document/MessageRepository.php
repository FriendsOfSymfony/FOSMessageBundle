<?php

namespace Bundle\Ornicar\MessageBundle\Document;

use Bundle\Ornicar\MessageBundle\Model\MessageRepositoryInterface;
use Zend\Paginator\Paginator;
use ZendPaginatorAdapter\DoctrineMongoDBAdapter;
use MongoId;

class MessageRepository implements MessageRepositoryInterface
{
    /**
     * @see MessageRepositoryInterface::findRecentUnreadByUser
     */
    public function findRecentUnreadByUser(User $user, $asPaginator = false)
    {
        $query = $this->createUserUnreadQuery($user)->sort('createdAt', 'DESC');

        if ($asPaginator) {
            return new Paginator(new DoctrineMongoDBAdapter($query));
        }

        return array_values($query->getQuery()->execute()->toArray());
    }

    public function countUnreadByUser(User $user)
    {
        return $this->createUserUnreadQuery($user)->getQuery()->count();
    }

    protected function createUserUnreadQuery(User $user)
    {
        return $this->createQueryBuilder()
            ->field('user.$id')->equals(new MongoId($user->getId()))
            ->field('isRead')->equals(false);
    }
}
