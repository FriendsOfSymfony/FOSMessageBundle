<?php

namespace Ornicar\MessageBundle\EntityManager;

use Ornicar\MessageBundle\ModelManager\ThreadManager as BaseThreadManager;
use Doctrine\ORM\EntityManager;
use Ornicar\MessageBundle\Model\ThreadInterface;
use Ornicar\MessageBundle\Model\ReadableInterface;
use Ornicar\MessageBundle\Model\ParticipantInterface;
use Doctrine\ORM\Query\Builder;


/**
 * Default ORM ThreadManager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ThreadManager extends BaseThreadManager
{
    /**
     * @var EntityManager
     */
    protected $em;

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
     * @param EntityManager     $em
     * @param string            $class
     * @param MessageManager    $messageManager
     */
    public function __construct(EntityManager $em, $class, MessageManager $messageManager)
    {
        $this->em             = $em;
        $this->repository     = $em->getRepository($class);
        $this->class          = $em->getClassMetadata($class)->name;
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
        return $this->repository->createQueryBuilder('t')
            ->innerJoin('t.metadata', 'm')
            ->innerJoin('m.participant', 'p')

            // the participant is in the thread participants
            ->andWhere('p.id = :user_id')
            ->setParameter('user_id', $participant->getId())

            // the thread does not contain spam or flood
            ->andWhere('t.isSpam = 0')

            // the thread is not deleted by this participant
            ->andWhere('m.threadDeleted = 0')

            // there is at least one message written by an other participant
            // TODO (need 2nd join to map table?)

            // sort by date of last message written by an other participant
            // TODO (need 2nd join to map table?)
        ;
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
        return $this->getParticipantInboxThreadsQueryBuilder($participant)
            ->getQuery()
            ->execute();
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
        return $this->repository->createQueryBuilder('t')
            ->innerJoin('t.metadata', 'm')
            ->innerJoin('m.participant', 'p')

            // the participant is in the thread participants
            ->andWhere('p.id = :user_id')
            ->setParameter('user_id', $participant->getId())

            // the thread does not contain spam or flood
            ->andWhere('t.isSpam = 0')

            // the thread is not deleted by this participant
            ->andWhere('m.threadDeleted = 0')

            // there is at least one message written by this participant
            ->andWhere('m.lastParticipantMessageDate IS NOT NULL')

            // sort by date of last message written by this participant
            ->orderBy('m.lastParticipantMessageDate', 'DESC')
        ;
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
        return $this->getParticipantSentThreadsQueryBuilder($participant)
            ->getQuery()
            ->execute();
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

        throw new \Exception('not yet implemented');
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
        return $this->getParticipantThreadsBySearchQueryBuilder($participant, $search)
            ->getQuery()
            ->execute();
    }

    /**
     * Gets threads created by a participant
     *
     * @param ParticipantInterface $participant
     * @return array of ThreadInterface
     */
    public function findThreadsCreatedBy(ParticipantInterface $participant)
    {
        throw new \Exception('not yet implemented');
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
        $this->em->persist($thread);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * Deletes a thread
     *
     * @param ThreadInterface $thread the thread to delete
     */
    public function deleteThread(ThreadInterface $thread)
    {
        $this->em->remove($thread);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified comment thread class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }


    /**
     * DENORMALIZATION
     *
     * All following methods are relative to denormalization
     */

    /**
     * Performs denormalization tricks
     */
    protected function denormalize(ThreadInterface $thread)
    {
        $this->doMetadata($thread);
        $this->doCreatedByAndAt($thread);
    }

    /**
     * Ensures that the thread metadata are up to date
     */
    protected function doMetadata(ThreadInterface $thread)
    {
        // Participants
        foreach ($thread->getParticipants() as $participant) {
            if (!$thread->hasMetadataForParticipant($participant)) {
                $metadata = $this->createThreadMetadata();
                $metadata->setParticipant($participant);

                $thread->replaceMetadata($metadata);
            }
        }

        // Messages
        foreach ($thread->getMessages() as $message) {
            if ($thread->hasMetadataForParticipant($message->getSender())) {
                $metadata = $thread->getMetadataForParticipant($message->getSender());
            } else {
                $metadata = $this->createThreadMetadata();
                $metadata->setParticipant($message->getSender());
            }

            $metadata->setLastParticipantMessageDate($message->getCreatedAt());
            $thread->replaceMetadata($metadata);
        }
    }

    /**
     * Ensures that the createdBy & createdAt properties are set
     */
    protected function doCreatedByAndAt(ThreadInterface $thread)
    {
        if (isset($thread->createdBy)
        or !($message = $thread->getFirstMessage())) {
            return;
        }

        $thread->setCreatedBy($message->getSender());
        $thread->setCreatedAt($message->getCreatedAt());
    }

    protected function createThreadMetadata()
    {
        $class = $this->getClass().'Metadata';
        return new $class();
    }

}
