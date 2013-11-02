<?php

namespace FOS\MessageBundle\EntityManager;

use Doctrine\ORM\EntityManager;
use FOS\MessageBundle\Entity\Thread;

/**
 * Handles de-normalizing the ORM thread objects.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class ThreadDenormalizer
{
    /**
     * The metadata model class
     *
     * @var string
     */
    protected $metaClass;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     * @param string $metaClass
     */
    public function __construct(EntityManager $em, $metaClass)
    {
        $this->metaClass = $em->getClassMetadata($metaClass)->name;
    }

    /**
     * Performs denormalization on a Doctrine ORM Thread entity.
     *
     * @param Thread $thread
     */
    public function denormalize(Thread $thread)
    {
        $this->doMetadata($thread);
        $this->doCreatedByAndAt($thread);
        $this->doDatesOfLastMessageWrittenByOtherParticipant($thread);
    }

    /**
     * Ensures that thread metadata is up to date and correct.
     *
     * @param Thread $thread
     */
    protected function doMetadata(Thread $thread)
    {
        // Participants
        foreach ($thread->getParticipants() as $participant) {
            /** @var \FOS\MessageBundle\Model\ParticipantInterface $participant */
            $meta = $thread->getMetadataForParticipant($participant);
            if (!$meta) {
                $meta = $this->createThreadMetadata();
                $meta->setParticipant($participant);

                $thread->addMetadata($meta);
            }
        }

        // Messages
        foreach ($thread->getMessages() as $message) {
            /** @var \FOS\MessageBundle\Model\MessageInterface $message */
            $meta = $thread->getMetadataForParticipant($message->getSender());
            if (!$meta) {
                $meta = $this->createThreadMetadata();
                $meta->setParticipant($message->getSender());
                $thread->addMetadata($meta);
            }

            $meta->setLastParticipantMessageDate($message->getCreatedAt());
        }
    }

    /**
     * Ensures that the createdBy & createdAt properties are set
     *
     * @param Thread $thread
     */
    protected function doCreatedByAndAt(Thread $thread)
    {
        if (!($message = $thread->getFirstMessage())) {
            return;
        }

        if (!$thread->getCreatedBy()) {
            $thread->setCreatedBy($message->getSender());
        }
        if (!$thread->getCreatedAt()) {
            $thread->setCreatedAt($message->getCreatedAt());
        }
    }

    /**
     * Update the dates of last message written by other participants
     *
     * @param Thread $thread
     */
    protected function doDatesOfLastMessageWrittenByOtherParticipant(Thread $thread)
    {
        foreach ($thread->getAllMetadata() as $meta) {
            /** @var \FOS\MessageBundle\Entity\ThreadMetadata $meta */
            $participant = $meta->getParticipant();
            $lastMessage = null;

            foreach ($thread->getMessages() as $message) {
                /** @var \FOS\MessageBundle\Entity\Message $message */
                if ($participant !== $message->getSender()) {
                    $lastMessage = $lastMessage ?
                        max($lastMessage, $message->getCreatedAt()) :
                        $message->getCreatedAt();
                }
            }

            if ($lastMessage) {
                $meta->setLastMessageDate($lastMessage);
            }
        }
    }

    /**
     * Creates a new Thread metadata object.
     *
     * @return \FOS\MessageBundle\Entity\ThreadMetadata
     */
    protected function createThreadMetadata()
    {
        return new $this->metaClass;
    }
}
