<?php

namespace Ornicar\MessageBundle\Entity;

use Ornicar\MessageBundle\Model\ThreadInterface;
use Ornicar\MessageBundle\Model\ThreadMetadata as BaseThreadMetadata;

abstract class ThreadMetadata extends BaseThreadMetadata
{
    protected $id;

    protected $thread;

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
}
