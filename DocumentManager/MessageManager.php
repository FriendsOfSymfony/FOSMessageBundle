<?php

namespace FOS\MessageBundle\DocumentManager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Query\Builder;
use FOS\MessageBundle\Document\Message;
use FOS\MessageBundle\Document\MessageMetadata;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ReadableInterface;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\ModelManager\MessageManager as BaseMessageManager;

/**
 * Default MongoDB MessageManager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class MessageManager extends BaseMessageManager
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
     * @param DocumentManager $dm
     * @param string          $class
     * @param string          $metaClass
     */
    public function __construct(DocumentManager $dm, $class, $metaClass)
    {
        $this->dm = $dm;
        $this->repository = $dm->getRepository($class);
        $this->class = $dm->getClassMetadata($class)->name;
        $this->metaClass = $dm->getClassMetadata($metaClass)->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbUnreadMessageByParticipant(ParticipantInterface $participant)
    {
        return $this->repository->createQueryBuilder()
            ->field('unreadForParticipants')->equals($participant->getId())
            ->getQuery()
            ->count();
    }

    /**
     * {@inheritdoc}
     */
    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        $this->markIsReadByParticipant($readable, $participant, true);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        $this->markIsReadByParticipant($readable, $participant, false);
    }

    /**
     * Marks all messages of this thread as read by this participant.
     *
     * @param ThreadInterface      $thread
     * @param ParticipantInterface $participant
     * @param bool                 $isRead
     */
    public function markIsReadByThreadAndParticipant(ThreadInterface $thread, ParticipantInterface $participant, $isRead)
    {
        $this->markIsReadByCondition($participant, $isRead, function (Builder $queryBuilder) use ($thread) {
            $queryBuilder->field('thread.$id')->equals(new \MongoId($thread->getId()));
        });
    }

    /**
     * Marks the message as read or unread by this participant.
     *
     * @param MessageInterface     $message
     * @param ParticipantInterface $participant
     * @param bool                 $isRead
     */
    protected function markIsReadByParticipant(MessageInterface $message, ParticipantInterface $participant, $isRead)
    {
        $this->markIsReadByCondition($participant, $isRead, function (Builder $queryBuilder) use ($message) {
            $queryBuilder->field('_id')->equals(new \MongoId($message->getId()));
        });
    }

    /**
     * Marks messages as read/unread
     * by updating directly the storage.
     *
     * @param ParticipantInterface $participant
     * @param bool                 $isRead
     * @param \Closure             $condition
     */
    protected function markIsReadByCondition(ParticipantInterface $participant, $isRead, \Closure $condition)
    {
        $queryBuilder = $this->repository->createQueryBuilder();
        $condition($queryBuilder);
        $queryBuilder->update()
            ->field('metadata.participant.$id')->equals(new \MongoId($participant->getId()));

        /* If marking the message as read for a participant, we should pull
         * their ID out of the unreadForParticipants array. The same is not
         * true for the inverse. We should only add a participant ID to this
         * array if the message is not considered spam.
         */
        if ($isRead) {
            $queryBuilder->field('unreadForParticipants')->pull($participant->getId());
        }

        $queryBuilder
            ->field('metadata.$.isRead')->set((bool) $isRead)
            ->getQuery(array('multiple' => true))
            ->execute();

        /* If marking the message as unread for a participant, add their ID to
         * the unreadForParticipants array if the message is not spam. This must
         * be done in a separate query, since the criteria is more selective.
         */
        if (!$isRead) {
            $queryBuilder = $this->repository->createQueryBuilder();
            $condition($queryBuilder);
            $queryBuilder->update()
                ->field('metadata.participant.$id')->equals(new \MongoId($participant->getId()))
                ->field('isSpam')->equals(false)
                ->field('unreadForParticipants')->addToSet($participant->getId())
                ->getQuery(array('multiple' => true))
                ->execute();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveMessage(MessageInterface $message, $andFlush = true)
    {
        $message->denormalize();
        $this->dm->persist($message);
        if ($andFlush) {
            $this->dm->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Creates a new MessageMetadata instance.
     *
     * @return MessageMetadata
     */
    protected function createMessageMetadata()
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
     * @param Message $message
     */
    public function denormalize(Message $message)
    {
        $this->doEnsureMessageMetadataExists($message);
        $message->denormalize();
    }

    /**
     * Ensures that the message has metadata for each thread participant.
     *
     * @param Message $message
     */
    protected function doEnsureMessageMetadataExists(Message $message)
    {
        if (!$thread = $message->getThread()) {
            throw new \InvalidArgumentException(sprintf('No thread is referenced in message with id "%s"', $message->getId()));
        }

        foreach ($thread->getParticipants() as $participant) {
            if (!$meta = $message->getMetadataForParticipant($participant)) {
                $meta = $this->createMessageMetadata();
                $meta->setParticipant($participant);
                $message->addMetadata($meta);
            }
        }
    }
}
