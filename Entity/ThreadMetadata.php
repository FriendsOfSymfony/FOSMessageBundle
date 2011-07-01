<?php

namespace Ornicar\MessageBundle\Entity;

use Ornicar\MessageBundle\Model\ParticipantInterface;
use Ornicar\MessageBundle\Model\ThreadInterface;


abstract class ThreadMetadata
{
    protected $id;

    protected $participant;
    protected $thread;

    protected $isDeleted = 0;

    protected $lastParticipantMessageDate;

    /**
     * Gets the thread map id
     *
     * @return integer
     **/
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ThreadInterface
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * @param  ThreadInterface
     * @return null
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }

    /**
     * @return ParticipantInterface
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * @param  ParticipantInterface
     * @return null
     */
    public function setParticipant(ParticipantInterface $participant)
    {
        $this->participant = $participant;
    }

    /**
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @param  boolean
     * @return null
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = (boolean)$isDeleted;
    }


    /**
     * @return DateTime
     */
    public function getLastParticipantMessageDate()
    {
        return $this->lastParticipantMessageDate;
    }

    /**
     * @param  DateTime
     * @return null
     */
    public function setLastParticipantMessageDate(\DateTime $date)
    {
        $this->lastParticipantMessageDate = $date;
    }

}
