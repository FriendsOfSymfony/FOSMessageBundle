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
        $query = $this->createQueryBuilder()
            ->sort('createdAt', 'DESC')
            ->field('isRead')->equals(false)
            ->field('user.$id')->equals(new MongoId($user->getId()));

        if ($asPaginator) {
            return new Paginator(new DoctrineMongoDBAdapter($query));
        }

        return array_values($query->getQuery()->execute()->toArray());
    }
}
