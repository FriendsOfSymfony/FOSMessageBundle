<?php

namespace Ornicar\MessageBundle\DocumentManager;

use Doctrine\ODM\MongoDB\DocumentManager;
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
     * Tells how many unread, non-spam, messages this participant has
     *
     * @param ParticipantInterface $participant
     * @return int the number of unread messages
     */
    public function getNbUnreadMessageByParticipant(ParticipantInterface $participant)
    {
        $isReadByParticipantFieldName = sprintf('isReadByParticipant.%s', $participant->getId());

        return $this->repository->createQueryBuilder()
            ->field($isReadByParticipantFieldName)->equals(false)
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
     * @param ParticipantInterface $user
     */
    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $user)
    {
        return $this->markIsReadByParticipant($readable, $user, true);
    }

    /**
     * Marks the readable as unread by this participant
     *
     * @param ReadableInterface $readable
     * @param ParticipantInterface $user
     */
    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $user)
    {
        return $this->markIsReadByParticipant($readable, $user, false);
    }

    /**
     * Marks all messages of this thread as read by this participant
     *
     * @param ThreadInterface $thread
     * @param ParticipantInterface $user
     * @param boolean $isRead
     */
    public function markIsReadByThreadAndParticipant(ThreadInterface $thread, ParticipantInterface $user, $isRead)
    {
        $this->markIsReadByCondition($user, $isRead, function(Builder $queryBuilder) use ($thread) {
            $queryBuilder->field('thread.$id')->equals(new \MongoId($thread->getId()));
        });
    }

    /**
     * Marks the message as read or unread by this participant
     *
     * @param MessageInterface $message
     * @param ParticipantInterface $user
     * @param boolean $isRead
     */
    protected function markIsReadByParticipant(MessageInterface $message, ParticipantInterface $user, $isRead)
    {
        $this->markIsReadByCondition($user, $isRead, function(Builder $queryBuilder) use ($message) {
            $queryBuilder->field('id')->equals($message->getId());
        });
    }

    /**
     * Marks messages as read/unread
     * by updating directly the storage
     *
     * @param ParticipantInterface $user
     * @param boolean $isRead
     * @param \Closure $condition
     */
    protected function markIsReadByCondition(ParticipantInterface $user, $isRead, \Closure $condition)
    {
        $isReadByParticipantFieldName = sprintf('isReadByParticipant.%s', $user->getId());
        $queryBuilder = $this->repository->createQueryBuilder();
        $condition($queryBuilder);
        $queryBuilder->update()
            ->field($isReadByParticipantFieldName)->set((boolean) $isRead)
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
}
