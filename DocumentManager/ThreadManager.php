<?php

namespace Ornicar\MessageBundle\DocumentManager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Ornicar\MessageBundle\Document\Thread;
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
     * @param string                  $metaClass
     * @param MessageManager          $messageManager
     */
    public function __construct(DocumentManager $dm, $class, $metaClass, MessageManager $messageManager)
    {
        $this->dm             = $dm;
        $this->repository     = $dm->getRepository($class);
        $this->class          = $dm->getClassMetadata($class)->name;
        $this->metaClass      = $dm->getClassMetadata($metaClass)->name;
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
     * Finds not deleted, non-spam threads for a participant,
     * containing at least one message not written by this participant,
     * ordered by last message not written by this participant in reverse order.
     * In one word: an inbox.
     *
     * @param ParticipantInterface $participant
     * @return Builder a query builder suitable for pagination
     */
    public function getParticipantInboxThreadsQueryBuilder(ParticipantInterface $participant)
    {
        return $this->repository->createQueryBuilder()
            // the participant hasn't deleted the thread, and another user wrote a message
            ->field('metadata')->elemMatch($this
                ->getNotDeletedByParticipantExpression($participant)
                ->field('lastMessageDate')->notEqual(null)
            )
            // the thread does not contain spam or flood
            ->field('isSpam')->equals(false)
            /* TODO: Sort by date of the last message written by another
             * participant, as is done for ORM. This is not possible with the
             * current schema; compromise by sorting by last message date.
             */
            ->sort('lastMessageDate', 'desc');
    }

    /**
     * Finds not deleted, non-spam threads for a participant,
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
        return $this->repository->createQueryBuilder()
            // the participant hasn't deleted the thread, and has written a message
            ->field('metadata')->elemMatch($this
                ->getNotDeletedByParticipantExpression($participant)
                ->field('lastParticipantMessageDate')->notEqual(null)
            )
            /* TODO: Sort by date of the last message written by this
             * participant, as is done for ORM. This is not possible with the
             * current schema; compromise by sorting by last message date.
             */
            ->sort('lastMessageDate', 'desc');
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

        return $this->repository->createQueryBuilder()
            // the thread is not deleted by this participant
            ->field('metadata')->elemMatch($this->getNotDeletedByParticipantExpression($participant))
            // TODO: this search is not anchored and uses no indexes
            ->field('keywords')->equals(new \MongoRegex($regex))
            /* TODO: Sort by date of the last message written by another
             * participant, as is done for ORM. This is not possible with the
             * current schema; compromise by sorting by last message date.
             */
            ->sort('lastMessageDate', 'desc');
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
     * Gets threads created by a participant
     *
     * @param ParticipantInterface $participant
     * @return array of ThreadInterface
     */
    public function findThreadsCreatedBy(ParticipantInterface $participant)
    {
        return $this->repository->createQueryBuilder()
            ->field('createdBy.$id')->equals(new \MongoId($participant->getId()))
            ->getQuery()
            ->execute();
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
    public function saveThread(ThreadInterface $thread, $andFlush = true)
    {
        $this->denormalize($thread);
        $this->dm->persist($thread);
        if ($andFlush) {
            $this->dm->flush(array('safe' => true));
        }
    }

    /**
     * Deletes a thread
     * This is not participant deletion but real deletion
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

    /**
     * Creates a new ThreadMetadata instance
     *
     * @return ThreadMetadata
     */
    protected function createThreadMetadata()
    {
        return new $this->metaClass();
    }

    /**
     * Creates an expression to match ThreadMetadata where the participant has
     * not deleted the thread.
     *
     * @param ParticipantInterface $participant
     * @return Doctrine\ODM\MongoDB\Query\Expr
     */
    protected function getNotDeletedByParticipantExpression(ParticipantInterface $participant)
    {
        return $this->dm->createQueryBuilder($this->metaClass)->expr()
            ->field('participant.$id')->equals(new \MongoId($participant->getId()))
            ->field('isDeleted')->equals(false);
    }

    /**
     * DENORMALIZATION
     *
     * All following methods are relative to denormalization
     */

    /**
     * Performs denormalization tricks
     *
     * @param Thread $thread
     */
    protected function denormalize(Thread $thread)
    {
        $this->doParticipants($thread);
        $this->doEnsureThreadMetadataExists($thread);
        $thread->denormalize();

        foreach ($thread->getMessages() as $message) {
            $this->messageManager->denormalize($message);
        }
    }

    /**
     * Ensures that the thread participants are up to date
     */
    protected function doParticipants(Thread $thread)
    {
        foreach ($thread->getMessages() as $message) {
            $thread->addParticipant($message->getSender());
        }
    }

    /**
     * Ensures that metadata exists for each thread participant and that the
     * last message dates are current
     *
     * @param Thread $thread
     */
    protected function doEnsureThreadMetadataExists(Thread $thread)
    {
        foreach ($thread->getParticipants() as $participant) {
            if (!$meta = $thread->getMetadataForParticipant($participant)) {
                $meta = $this->createThreadMetadata();
                $meta->setParticipant($participant);
                $thread->addMetadata($meta);
            }
        }
    }
}
