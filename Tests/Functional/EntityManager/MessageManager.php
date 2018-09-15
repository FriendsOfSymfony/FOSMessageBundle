<?php

namespace FOS\MessageBundle\Tests\Functional\EntityManager;

use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ReadableInterface;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\ModelManager\MessageManager as BaseMessageManager;
use FOS\MessageBundle\Tests\Functional\Entity\Message;

/**
 * Default ORM MessageManager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class MessageManager extends BaseMessageManager
{
    public function getNbUnreadMessageByParticipant(ParticipantInterface $participant)
    {
        return 3;
    }

    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
    }

    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
    }

    public function markIsReadByThreadAndParticipant(ThreadInterface $thread, ParticipantInterface $participant, $isRead)
    {
    }

    protected function markIsReadByParticipant(MessageInterface $message, ParticipantInterface $participant, $isRead)
    {
    }

    public function saveMessage(MessageInterface $message, $andFlush = true)
    {
    }

    public function getClass()
    {
        return Message::class;
    }
}
