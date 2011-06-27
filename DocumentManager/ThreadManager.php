<?php

namespace Ornicar\MessageBundle\DocumentManager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Ornicar\MessageBundle\Model\ThreadInterface;
use Ornicar\MessageBundle\Model\ReadableInterface;
use Ornicar\MessageBundle\ModelManager\ThreadManager as BaseThreadManager;
use Ornicar\MessageBundle\Model\ParticipantInterface;
use Doctrine\ODM\MongoDB\Query\Builder;

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
     * Finds not deleted threads for a participant,
     * containing at least one message not written by this participant,
     * ordered by last message not written by this participant in reverse order.
     * In one word: an inbox.
     *
     * @param ParticipantInterface $participant
     * @return Builder a query builder suitable for pagination
     */
    public function getParticipantInboxThreadsQueryBuilder(ParticipantInterface $participant)
    {
        $isDeletedByParticipantFieldName = sprintf('isDeletedByParticipant.%s', $participant->getId());
        $datesOfLastMessageWrittenByOtherParticipantFieldName = sprintf('datesOfLastMessageWrittenByOtherParticipant.%s', $participant->getId());

        return $this->repository->createQueryBuilder()
            // the participant is in the thread participants
            ->field('participants.$id')->equals(new \MongoId($participant->getId()))
            // the thread is not deleted by this participant
            ->field($isDeletedByParticipantFieldName)->equals(false)
            // there is at least one message written by an other participant
            ->field($datesOfLastMessageWrittenByOtherParticipantFieldName)->exists(true)
            // sort by date of last message written by an other participant
            ->sort($datesOfLastMessageWrittenByOtherParticipantFieldName, 'desc');
    }

    /**
     * Finds not deleted threads for a participant,
     * containing at least one message not written by this participant,
     * ordered by last message not written by this participant in reverse order.
     * In one word: an inbox.
     *
     * @param ParticipantInterface $participant
     * @return array of ThreadInterface
     */
    public function findParticipantInboxThreads(ParticipantInterface $participant)
    {
        return $this->getParticipantInboxThreadsQueryBuilder($participant)->getQuery()->execute();
    }

    /**
     * Finds not deleted threads from a participant,
     * containing at least one message written by this participant,
     * ordered by last message written by this participant in reverse order.
     * In one word: an sentbox.
     *
     * @param ParticipantInterface $participant
     * @return Builder a query builder suitable for pagination
     */
    public function getParticipantSentThreadsQueryBuilder(ParticipantInterface $participant)
    {
        $isDeletedByParticipantFieldName = sprintf('isDeletedByParticipant.%s', $participant->getId());
        $datesOfLastMessageWrittenByParticipantFieldName = sprintf('datesOfLastMessageWrittenByParticipant.%s', $participant->getId());

        return $this->repository->createQueryBuilder()
            // the participant is in the thread participants
            ->field('participants.$id')->equals(new \MongoId($participant->getId()))
            // the thread is not deleted by this participant
            ->field($isDeletedByParticipantFieldName)->equals(false)
            // there is at least one message written by this participant
            ->field($datesOfLastMessageWrittenByParticipantFieldName)->exists(true)
            // sort by date of last message written by this participant
            ->sort($datesOfLastMessageWrittenByParticipantFieldName, 'desc');
    }

    /**
     * Finds not deleted threads from a participant,
     * containing at least one message written by this participant,
     * ordered by last message written by this participant in reverse order.
     * In one word: an sentbox.
     *
     * @param ParticipantInterface $participant
     * @return array of ThreadInterface
     */
    public function findParticipantSentThreads(ParticipantInterface $participant)
    {
        return $this->getParticipantSentThreadsQueryBuilder($participant)->getQuery()->execute();
    }

    /**
     * Finds not deleted threads for a participant,
     * matching the given search term
     * ordered by last message not written by this participant in reverse order.
     *
     * @param ParticipantInterface $participant
     * @param string $search
     * @return Builder a query builder suitable for pagination
     */
    public function getParticipantThreadsBySearchQueryBuilder(ParticipantInterface $participant, $search)
    {
        // remove all non-word chars
        $search = preg_replace('/[^\w]/', ' ', trim($search));
        // build a regex like (term1|term2)
        $regex = sprintf('/(%s)/', implode('|', explode(' ', $search)));

        $isDeletedByParticipantFieldName = sprintf('isDeletedByParticipant.%s', $participant->getId());
        $datesOfLastMessageWrittenByOtherParticipantFieldName = sprintf('datesOfLastMessageWrittenByOtherParticipant.%s', $participant->getId());

        return $this->repository->createQueryBuilder()
            // the participant is in the thread participants
            ->field('participants.$id')->equals(new \MongoId($participant->getId()))
            // the thread is not deleted by this participant
            ->field($isDeletedByParticipantFieldName)->equals(false)
            // sort by date of last message written by an other participant
            ->sort($datesOfLastMessageWrittenByOtherParticipantFieldName, 'desc')
            ->field('keywords')->equals(new \MongoRegex($regex));
    }

    /**
     * Finds not deleted threads for a participant,
     * matching the given search term
     * ordered by last message not written by this participant in reverse order.
     *
     * @param ParticipantInterface $participant
     * @param string $search
     * @return array of ThreadInterface
     */
    public function findParticipantThreadsBySearch(participantinterface $participant, $search)
    {
        return $this->getParticipantThreadsBySearchQueryBuilder($participant, $search)->getQuery()->execute();
    }

    /**
     * Marks the readable as read by this participant
     * Must be applied directly to the storage,
     * without modifying the readable state.
     * We want to show the unread readables on the page,
     * as well as marking the as read.
     *
     * @param ReadableInterface $readable
     * @param ParticipantInterface $participant
     */
    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        return $this->messageManager->markIsReadByThreadAndParticipant($readable, $participant, true);
    }

    /**
     * Marks the readable as unread by this participant
     *
     * @param ReadableInterface $readable
     * @param ParticipantInterface $participant
     */
    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        return $this->messageManager->markIsReadByThreadAndParticipant($readable, $participant, false);
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
