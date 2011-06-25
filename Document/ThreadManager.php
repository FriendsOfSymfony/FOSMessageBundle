<?php

namespace Ornicar\MessageBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use Ornicar\MessageBundle\Model\ThreadInterface;
use Ornicar\MessageBundle\Model\ThreadManager as BaseThreadManager;
use FOS\UserBundle\Model\UserInterface;

/**
 * Default ODM ThreadManager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ThreadManager extends BaseThreadManager
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var DocumentRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param DocumentManager         $dm
     * @param string                  $class
     */
    public function __construct(DocumentManager $dm, $class)
    {
        $this->dm         = $dm;
        $this->repository = $dm->getRepository($class);
        $this->class      = $dm->getClassMetadata($class)->name;
    }

    /**
     * Finds a thread by its ID
     *
     * @return ThreadInterface or null
     */
    public function findThreadById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds threads for a user,
     * containing at least one message not written by this user,
     * ordered by last message not written by this user in reverse order.
     * In one word: an inbox.
     *
     * @param UserInterface $user
     * @return Builder a query builder suitable for pagination
     */
    public function getUserInboxThreadsQueryBuilder(UserInterface $user)
    {
        $datesOfLastMessageWrittenByOtherUserFieldName = sprintf('datesOfLastMessageWrittenByOtherUser.%s', $user->getId());
        return $this->repository->createQueryBuilder()
            ->field('participants.$id')->equals(new \MongoId($user->getId()))
            ->field($datesOfLastMessageWrittenByOtherUserFieldName)->exists(true)
            ->sort($datesOfLastMessageWrittenByOtherUserFieldName, 'desc');
    }

    /**
     * Finds threads for a user,
     * containing at least one message not written by this user,
     * ordered by last message not written by this user in reverse order.
     * In one word: an inbox.
     *
     * @param UserInterface $user
     * @return array of ThreadInterface
     */
    public function findUserInboxThreads(UserInterface $user)
    {
        return $this->getUserInboxThreadsQueryBuilder($user)->getQuery()->execute();
    }

    /**
     * Finds threads from a user,
     * containing at least one message written by this user,
     * ordered by last message written by this user in reverse order.
     * In one word: an sentbox.
     *
     * @param UserInterface $user
     * @return Builder a query builder suitable for pagination
     */
    public function getUserSentThreadsQueryBuilder(UserInterface $user)
    {
        $datesOfLastMessageWrittenByUserFieldName = sprintf('datesOfLastMessageWrittenByUser.%s', $user->getId());
        return $this->repository->createQueryBuilder()
            ->field('participants.$id')->equals(new \MongoId($user->getId()))
            ->field($datesOfLastMessageWrittenByUserFieldName)->exists(true)
            ->sort($datesOfLastMessageWrittenByUserFieldName, 'desc');
    }

    /**
     * Finds threads from a user,
     * containing at least one message written by this user,
     * ordered by last message written by this user in reverse order.
     * In one word: an sentbox.
     *
     * @param UserInterface $user
     * @return array of ThreadInterface
     */
    public function findUserSentThreads(UserInterface $user)
    {
        return $this->getUserSentThreadsQueryBuilder($user)->getQuery()->execute();
    }

    /**
     * Saves a thread
     *
     * @param ThreadInterface $thread
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    public function updateThread(ThreadInterface $thread, $andFlush = true)
    {
        $this->dm->persist($thread);
        if ($andFlush) {
            $this->dm->flush();
        }
    }

    /**
     * Deletes a thread
     *
     * @param ThreadInterface $thread the thread to delete
     */
    public function deleteThread(ThreadInterface $thread)
    {
        $this->dm->remove($thread);
        $this->dm->flush();
    }

    /**
     * Returns the fully qualified comment thread class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
