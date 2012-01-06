<?php

namespace Ornicar\MessageBundle\DocumentManager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Ornicar\MessageBundle\Document\Message;
use Ornicar\MessageBundle\Model\MessageInterface;
use Ornicar\MessageBundle\ModelManager\MessageManager as BaseMessageManager;
use Ornicar\MessageBundle\Model\ReadableInterface;
use Ornicar\MessageBundle\Model\ParticipantInterface;
use Ornicar\MessageBundle\Model\ThreadInterface;
use Doctrine\ODM\MongoDB\Query\Builder;

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
     * Constructor.
     *
     * @param DocumentManager         $dm
     * @param string                  $class
     * @param string                  $metaClass
     */
    public function __construct(DocumentManager $dm, $class, $metaClass)
    {
        $this->dm         = $dm;
        $this->repository = $dm->getRepository($class);
        $this->class      = $dm->getClassMetadata($class)->name;
        $this->metaClass  = $dm->getClassMetadata($metaClass)->name;
    }

    /**
     * Tells how many unread, non-spam, messages this participant has
     *
     * @param ParticipantInterface $participant
     * @return int the number of unread messages
     */
    public function getNbUnreadMessageByParticipant(ParticipantInterface $participant)
    {
        $queryBuilder = $this->repository->createQueryBuilder();
        $metaQueryBuilder = $this->dm->createQueryBuilder($this->metaClass);

        return $queryBuilder
            ->field('metadata')->elemMatch($metaQueryBuilder->expr()
                ->field('participant.$id')->equals(new \MongoId($participant->getId()))
                ->field('isRead')->equals(false)
            )
            ->field('isSpam')->equals(false)
            ->getQuery()
            ->count();
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
        return $this->markIsReadByParticipant($readable, $participant, true);
    }

    /**
     * Marks the readable as unread by this participant
     *
     * @param ReadableInterface $readable
     * @param ParticipantInterface $participant
     */
    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        return $this->markIsReadByParticipant($readable, $participant, false);
    }

    /**
     * Marks all messages of this thread as read by this participant
     *
     * @param ThreadInterface $thread
     * @param ParticipantInterface $participant
     * @param boolean $isRead
     */
    public function markIsReadByThreadAndParticipant(ThreadInterface $thread, ParticipantInterface $participant, $isRead)
    {
        $this->markIsReadByCondition($participant, $isRead, function(Builder $queryBuilder) use ($thread) {
            $queryBuilder->field('thread.$id')->equals(new \MongoId($thread->getId()));
        });
    }

    /**
     * Marks the message as read or unread by this participant
     *
     * @param MessageInterface $message
     * @param ParticipantInterface $participant
     * @param boolean $isRead
     */
    protected function markIsReadByParticipant(MessageInterface $message, ParticipantInterface $participant, $isRead)
    {
        $this->markIsReadByCondition($participant, $isRead, function(Builder $queryBuilder) use ($message) {
            $queryBuilder->field('_id')->equals(new \MongoId($message->getId()));
        });
    }

    /**
     * Marks messages as read/unread
     * by updating directly the storage
     *
     * @param ParticipantInterface $participant
     * @param boolean $isRead
     * @param \Closure $condition
     */
    protected function markIsReadByCondition(ParticipantInterface $participant, $isRead, \Closure $condition)
    {
        $queryBuilder = $this->repository->createQueryBuilder();
        $condition($queryBuilder);
        $queryBuilder->update()
            ->field('metadata.participant.$id')->equals(new \MongoId($participant->getId()))
            ->field('metadata.$.isRead')->set((boolean) $isRead)
            ->getQuery(array('multiple' => true))
            ->execute();
    }

    /**
     * Saves a message
     *
     * @param MessageInterface $message
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    public function saveMessage(MessageInterface $message, $andFlush = true)
    {
        $message->denormalize();
        $this->dm->persist($message);
        if ($andFlush) {
            $this->dm->flush(array('safe' => true));
        }
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
     * Creates a new MessageMetadata instance
     *
     * @return MessageMetadata
     */
    protected function createMessageMetadata()
    {
        return new $this->metaClass();
    }

    /**
     * DENORMALIZATION
     *
     * All following methods are relative to denormalization
     */

    /**
     * Performs denormalization tricks
     *
     * @param Message $message
     */
    public function denormalize(Message $message)
    {
        $this->doEnsureMessageMetadataExists($message);
        $message->denormalize();
    }

    /**
     * Ensures that the message has metadata for each thread participant
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
