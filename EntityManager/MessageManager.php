<?php

namespace FOS\MessageBundle\EntityManager;

use FOS\MessageBundle\ModelManager\MessageManager as BaseMessageManager;
use Doctrine\ORM\EntityManager;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ReadableInterface;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ThreadInterface;
use Doctrine\ORM\Query\Builder;

/**
 * Default ORM MessageManager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class MessageManager extends BaseMessageManager
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
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $metaClass;

    /**
     * @param EntityManager     $em
     * @param string            $class
     * @param string            $metaClass
     */
    public function __construct(EntityManager $em, $class, $metaClass)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository($class);
        $this->class      = $em->getClassMetadata($class)->name;
        $this->metaClass  = $em->getClassMetadata($metaClass)->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getNbUnreadMessageByParticipant(ParticipantInterface $participant)
    {
        $builder = $this->repository->createQueryBuilder('m');

        return (int)$builder
            ->select($builder->expr()->count('mm.id'))

            ->innerJoin('m.metadata', 'mm')
            ->innerJoin('mm.participant', 'p')

            ->where('p.id = :participant_id')
            ->setParameter('participant_id', $participant->getId())

            ->andWhere('m.sender != :sender')
            ->setParameter('sender', $participant->getId())

            ->andWhere('mm.isRead = :isRead')
            ->setParameter('isRead', false, \PDO::PARAM_BOOL)

            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritDoc}
     */
    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        $readable->setIsReadByParticipant($participant, true);
    }

    /**
     * {@inheritDoc}
     */
    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        $readable->setIsReadByParticipant($participant, false);
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
        foreach ($thread->getMessages() as $message) {
            $this->markIsReadByParticipant($message, $participant, $isRead);
        }
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
        $meta = $message->getMetadataForParticipant($participant);
        if (!$meta || $meta->getIsRead() == $isRead) {
            return;
        }

        $this->em->createQueryBuilder()
            ->update($this->metaClass, 'm')
            ->set('m.isRead', '?1')
            ->setParameter('1', (bool)$isRead, \PDO::PARAM_BOOL)

            ->where('m.id = :id')
            ->setParameter('id', $meta->getId())

            ->getQuery()
            ->execute();
    }

    /**
     * {@inheritDoc}
     */
    public function saveMessage(MessageInterface $message, $andFlush = true)
    {
        $this->denormalize($message);
        $this->em->persist($message);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /*
     * DENORMALIZATION
     *
     * All following methods are relative to denormalization
     */

    /**
     * Performs denormalization tricks
     */
    protected function denormalize(MessageInterface $message)
    {
        $this->doMetadata($message);
    }

    /**
     * Ensures that the message metadata are up to date
     */
    protected function doMetadata(MessageInterface $message)
    {
        foreach ($message->getThread()->getAllMetadata() as $threadMeta) {
            $meta = $message->getMetadataForParticipant($threadMeta->getParticipant());
            if (!$meta) {
                $meta = $this->createMessageMetadata();
                $meta->setParticipant($threadMeta->getParticipant());

                $message->addMetadata($meta);
            }
        }
    }

    protected function createMessageMetadata()
    {
        return new $this->metaClass();
    }
}
