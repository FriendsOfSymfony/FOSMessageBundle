<?php

namespace Ornicar\MessageBundle\DocumentManager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Ornicar\MessageBundle\Model\ThreadInterface;
use Ornicar\MessageBundle\Model\ReadableInterface;
use Ornicar\MessageBundle\ModelManager\ThreadManager as BaseThreadManager;
use Ornicar\MessageBundle\Model\ParticipantInterface;

/**
 * Default MongoDB ThreadManager.
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
     * The model class
     *
     * @var string
     */
    protected $class;

    /**
     * The message manager, required to mark
     * the messages of a thread as read/unread
     *
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * Constructor.
     *
     * @param DocumentManager         $dm
     * @param string                  $class
     * @param MessageManager          $messageManager
     */
    public function __construct(DocumentManager $dm, $class, MessageManager $messageManager)
    {
        $this->dm             = $dm;
        $this->repository     = $dm->getRepository($class);
        $this->class          = $dm->getClassMetadata($class)->name;
        $this->messageManager = $messageManager;
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
     * Finds not deleted threads for a user,
     * containing at least one message not written by this user,
     * ordered by last message not written by this user in reverse order.
     * In one word: an inbox.
     *
     * @param ParticipantInterface $user
     * @return Builder a query builder suitable for pagination
     */
    public function getParticipantInboxThreadsQueryBuilder(ParticipantInterface $user)
    {
        $isDeletedByParticipantFieldName = sprintf('isDeletedByParticipant.%s', $user->getId());
        $datesOfLastMessageWrittenByOtherParticipantFieldName = sprintf('datesOfLastMessageWrittenByOtherParticipant.%s', $user->getId());

        return $this->repository->createQueryBuilder()
            // the participant is in the thread participants
            ->field('participants.$id')->equals(new \MongoId($user->getId()))
            // the thread is not deleted by this participant
            ->field($isDeletedByParticipantFieldName)->equals(false)
            // there is at least one message written by an other participant
            ->field($datesOfLastMessageWrittenByOtherParticipantFieldName)->exists(true)
            // sort by date of last message written by an other participant
            ->sort($datesOfLastMessageWrittenByOtherParticipantFieldName, 'desc');
    }

    /**
     * Finds not deleted threads for a user,
     * containing at least one message not written by this user,
     * ordered by last message not written by this user in reverse order.
     * In one word: an inbox.
     *
     * @param ParticipantInterface $user
     * @return array of ThreadInterface
     */
    public function findParticipantInboxThreads(ParticipantInterface $user)
    {
        return $this->getParticipantInboxThreadsQueryBuilder($user)->getQuery()->execute();
    }

    /**
     * Finds threads from a user,
     * containing at least one message written by this user,
     * ordered by last message written by this user in reverse order.
     * In one word: an sentbox.
     *
     * @param ParticipantInterface $user
     * @return Builder a query builder suitable for pagination
     */
    public function getParticipantSentThreadsQueryBuilder(ParticipantInterface $user)
    {
        $datesOfLastMessageWrittenByParticipantFieldName = sprintf('datesOfLastMessageWrittenByParticipant.%s', $user->getId());
        return $this->repository->createQueryBuilder()
            ->field('participants.$id')->equals(new \MongoId($user->getId()))
            ->field($datesOfLastMessageWrittenByParticipantFieldName)->exists(true)
            ->sort($datesOfLastMessageWrittenByParticipantFieldName, 'desc');
    }

    /**
     * Finds threads from a user,
     * containing at least one message written by this user,
     * ordered by last message written by this user in reverse order.
     * In one word: an sentbox.
     *
     * @param ParticipantInterface $user
     * @return array of ThreadInterface
     */
    public function findParticipantSentThreads(ParticipantInterface $user)
    {
        return $this->getParticipantSentThreadsQueryBuilder($user)->getQuery()->execute();
    }

    /**
     * Marks the readable as read by this participant
     * Must be applied directly to the storage,
     * without modifying the readable state.
     * We want to show the unread readables on the page,
     * as well as marking the as read.
     *
     * @param ReadableInterface $readable
     * @param ParticipantInterface $user
     */
    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $user)
    {
        return $this->messageManager->markIsReadByThreadAndParticipant($readable, $user, true);
    }

    /**
     * Marks the readable as unread by this participant
     *
     * @param ReadableInterface $readable
     * @param ParticipantInterface $user
     */
    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $user)
    {
        return $this->messageManager->markIsReadByThreadAndParticipant($readable, $user, false);
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
