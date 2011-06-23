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
     * Find threads for a user
     * Order them by last message not written by this user
     *
     * @param UserInterface $user
     * @return Builder a query builder suitable for pagination
     */
    public function getUserInboxThreadsQueryBuilder(UserInterface $user)
    {
        return $this->repository->createQueryBuilder()
            ->field('participants.$id')->equals(new \MongoId($user->getId()))
            ->sort(sprintf('datesOfLastMessageWrittenByOtherUser.%s', $user->getId()), 'desc');
    }

    /**
     * Find threads for a user
     * Order them by last message not written by this user
     *
     * @param UserInterface $user
     * @return array of ThreadInterface
     */
    public function findUserInboxThreads(UserInterface $user)
    {
        return $this->getUserInboxThreadsQueryBuilder($user)->getQuery()->execute();
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
