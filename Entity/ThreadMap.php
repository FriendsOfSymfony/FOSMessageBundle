<?php

namespace Ornicar\MessageBundle\Entity;

abstract class ThreadMap
{
    protected $id;

    protected $participant;
    protected $thread;

    protected $threadDeleted;

    protected $lastParticipantMessageDate;
}
