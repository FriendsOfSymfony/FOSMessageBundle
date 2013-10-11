<?php

namespace FOS\MessageBundle\EntityManager;

use FOS\MessageBundle\ModelManager\MessageManager as BaseMessageManager;
use Doctrine\ORM\EntityManager;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ReadableInterface;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ThreadInterface;

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
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var MessageDenormalizer
     */
    protected $denormalizer;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     * @param string $class
     * @param MessageDenormalizer $denormalizer
     */
    public function __construct(EntityManager $em, $class, MessageDenormalizer $denormalizer)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->class = $em->getClassMetadata($class)->name;
        $this->denormalizer = $denormalizer;
    }

    /**
     * Tells how many unread messages this participant has
     *
     * @param ParticipantInterface $participant
     * @return int the number of unread messages
     */
    public function getNbUnreadMessageByParticipant(ParticipantInterface $participant)
    {
        $builder = $this->repository->createQueryBuilder('m');

        return (int) $builder
            ->select($builder->expr()->count('mm.id'))

            ->innerJoin('m.metadata', 'mm')
            ->innerJoin('mm.participant', 'p')

            ->where('mm.participant = :participant')
            ->setParameter('participant', $participant)

            ->andWhere('m.sender != :sender')
            ->setParameter('sender', $participant->getId())

            ->andWhere('mm.isRead = :isRead')
            ->setParameter('isRead', false, \PDO::PARAM_BOOL)

            ->getQuery()
            ->getSingleScalarResult();
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
        $readable->setIsReadByParticipant($participant, true);
    }

    /**
     * Marks the readable as unread by this participant
     *
     * @param ReadableInterface $readable
     * @param ParticipantInterface $participant
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
        $this->denormalizer->denormalize($message);

        $meta = $message->getMetadataForParticipant($participant);
        if (!$meta) {
            return;
        }

        $message->setIsReadByParticipant($participant, $isRead);

        $this->saveMessage($message);
    }

    /**
     * Saves a message
     *
     * @param MessageInterface $message
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    public function saveMessage(MessageInterface $message, $andFlush = true)
    {
        $this->denormalizer->denormalize($message);

        $this->em->persist($message);
        if ($andFlush) {
            $this->em->flush();
        }
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
}
