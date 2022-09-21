<?php

namespace FOS\MessageBundle\Tests\Functional\EntityManager;

use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ReadableInterface;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\ModelManager\ThreadManager as BaseThreadManager;
use FOS\MessageBundle\Tests\Functional\Entity\Thread;

class ThreadManager extends BaseThreadManager
{
    public function findThreadById($id)
    {
        return new Thread();
    }

    public function getParticipantInboxThreadsQueryBuilder(ParticipantInterface $participant)
    {
    }

    public function findParticipantInboxThreads(ParticipantInterface $participant)
    {
        return [new Thread()];
    }

    public function getParticipantSentThreadsQueryBuilder(ParticipantInterface $participant)
    {
    }

    public function findParticipantSentThreads(ParticipantInterface $participant)
    {
        return [];
    }

    public function getParticipantDeletedThreadsQueryBuilder(ParticipantInterface $participant)
    {
    }

    public function findParticipantDeletedThreads(ParticipantInterface $participant)
    {
        return [];
    }

    public function getParticipantThreadsBySearchQueryBuilder(ParticipantInterface $participant, $search)
    {
    }

    public function findParticipantThreadsBySearch(ParticipantInterface $participant, $search)
    {
        return [];
    }

    public function findThreadsCreatedBy(ParticipantInterface $participant)
    {
        return [];
    }

    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
    }

    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
    }

    public function saveThread(ThreadInterface $thread, $andFlush = true)
    {
    }

    public function deleteThread(ThreadInterface $thread)
    {
    }

    public function getClass()
    {
        return Thread::class;
    }
}
