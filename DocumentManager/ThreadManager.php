<?php

namespace FOS\MessageBundle\DocumentManager;

use Doctrine\ODM\MongoDB\DocumentManager;
use FOS\MessageBundle\Document\Thread;
use FOS\MessageBundle\Document\ThreadMetadata;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Model\ReadableInterface;
use FOS\MessageBundle\ModelManager\ThreadManager as BaseThreadManager;
use FOS\MessageBundle\Model\ParticipantInterface;

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
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $metaClass;

    /**
     * The message manager, required to mark thread messages as read/unread.
     *
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * Constructor.
     *
     * @param DocumentManager $dm
     * @param string          $class
     * @param string          $metaClass
     * @param MessageManager  $messageManager
     */
    public function __construct(DocumentManager $dm, $class, $metaClass, MessageManager $messageManager)
    {
        $this->dm = $dm;
        $this->repository = $dm->getRepository($class);
        $this->class = $dm->getClassMetadata($class)->name;
        $this->metaClass = $dm->getClassMetadata($metaClass)->name;
        $this->messageManager = $messageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function findThreadById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantInboxThreadsQueryBuilder(ParticipantInterface $participant)
    {
        return $this->repository->createQueryBuilder()
            ->field('activeRecipients')->equals($participant->getId())
            /* TODO: Sort by date of the last message written by another
             * participant, as is done for ORM. This is not possible with the
             * current schema; compromise by sorting by last message date.
             */
            ->sort('lastMessageDate', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantInboxThreads(ParticipantInterface $participant)
    {
        return $this->getParticipantInboxThreadsQueryBuilder($participant)->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantSentThreadsQueryBuilder(ParticipantInterface $participant)
    {
        return $this->repository->createQueryBuilder()
            ->field('activeSenders')->equals($participant->getId())
            /* TODO: Sort by date of the last message written by this
             * participant, as is done for ORM. This is not possible with the
             * current schema; compromise by sorting by last message date.
             */
            ->sort('lastMessageDate', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantSentThreads(ParticipantInterface $participant)
    {
        return $this->getParticipantSentThreadsQueryBuilder($participant)->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantDeletedThreadsQueryBuilder(ParticipantInterface $participant)
    {
        return $this->repository->createQueryBuilder()
            ->field('metadata.isDeleted')->equals(true)
            ->field('metadata.participant.$id')->equals(new \MongoId($participant->getId()))
            ->sort('lastMessageDate', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantDeletedThreads(ParticipantInterface $participant)
    {
        return $this->getParticipantDeletedThreadsQueryBuilder($participant)->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantThreadsBySearchQueryBuilder(ParticipantInterface $participant, $search)
    {
        // remove all non-word chars
        $search = preg_replace('/[^\w]/', ' ', trim($search));
        // build a regex like (term1|term2)
        $regex = sprintf('/(%s)/', implode('|', explode(' ', $search)));

        return $this->repository->createQueryBuilder()
            ->field('activeParticipants')->equals($participant->getId())
            // Note: This query is not anchored, so "keywords" need not be indexed
            ->field('keywords')->equals(new \MongoRegex($regex))
            /* TODO: Sort by date of the last message written by this
             * participant, as is done for ORM. This is not possible with the
             * current schema; compromise by sorting by last message date.
             */
            ->sort('lastMessageDate', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantThreadsBySearch(ParticipantInterface $participant, $search)
    {
        return $this->getParticipantThreadsBySearchQueryBuilder($participant, $search)->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function findThreadsCreatedBy(ParticipantInterface $participant)
    {
        return $this->repository->createQueryBuilder()
            ->field('createdBy.$id')->equals(new \MongoId($participant->getId()))
            ->getQuery()
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        return $this->messageManager->markIsReadByThreadAndParticipant($readable, $participant, true);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        return $this->messageManager->markIsReadByThreadAndParticipant($readable, $participant, false);
    }

    /**
     * {@inheritdoc}
     */
    public function saveThread(ThreadInterface $thread, $andFlush = true)
    {
        $this->denormalize($thread);
        $this->dm->persist($thread);
        if ($andFlush) {
            $this->dm->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteThread(ThreadInterface $thread)
    {
        $this->dm->remove($thread);
        $this->dm->flush();
    }

    /**
     * Returns the fully qualified comment thread class name.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Creates a new ThreadMetadata instance.
     *
     * @return ThreadMetadata
     */
    protected function createThreadMetadata()
    {
        return new $this->metaClass();
    }

    /*
     * DENORMALIZATION
     *
     * All following methods are relative to denormalization
     */

    /**
     * Performs denormalization tricks.
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
     * Ensures that the thread participants are up to date.
     */
    protected function doParticipants(Thread $thread)
    {
        foreach ($thread->getMessages() as $message) {
            $thread->addParticipant($message->getSender());
        }
    }

    /**
     * Ensures that metadata exists for each thread participant and that the
     * last message dates are current.
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
