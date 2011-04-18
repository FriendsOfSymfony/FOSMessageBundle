<?php

namespace Ornicar\MessageBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Ornicar\MessageBundle\Model\MessageRepositoryInterface;
use FOS\UserBundle\Model\User;
use Zend\Paginator\Paginator;
use ZendPaginatorAdapter\DoctrineORMAdapter;

class MessageRepository extends EntityRepository implements MessageRepositoryInterface
{
    /**
     * @see MessageRepositoryInterface::findRecentByUser
     */
    public function findRecentByUser(User $user, $asPaginator = false)
    {
        $query = $this
            ->createByUserQuery($user, 'm')
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();

        if ($asPaginator) {
            return new Paginator(new DoctrineORMAdapter($query));
        }

        return array_values($query->execute()->toArray());
    }

    /**
     * @see MessageRepositoryInterface::findRecentSentByUser
     */
    public function findRecentSentByUser(User $user, $asPaginator = false)
    {
        $query = $this
            ->createSentByUserQuery($user, 'm')
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();

        if ($asPaginator) {
            return new Paginator(new DoctrineORMAdapter($query));
        }

        return array_values($query->execute()->toArray());
    }

    /**
     * @see MessageRepositoryInterface::countUnreadByUser
     */
    public function countUnreadByUser(User $user)
    {
        return $this
            ->createByUserUnreadQuery($user)
            ->getQuery()
            ->count();
    }

    public function createNewMessage()
    {
        $class = $this->getEntityName();
        return new $class();
    }

    protected function createByUserQuery(User $user, $alias='m')
    {
        return $this
            ->createQueryBuilder($alias)
            ->where($alias.'.to = :user_id')
            ->setParameter('user_id', $user->getId());
    }

    protected function createSentByUserQuery(User $user, $alias='m')
    {
        return $this
            ->createQueryBuilder($alias)
            ->where($alias.'.from = :user_id')
            ->setParameter('user_id', $user->getId());
    }

    protected function createByUserUnreadQuery(User $user)
    {
        return $this
            ->createByUserQuery($user, 'm')
            ->where('m.isRead = 0');
    }
}
