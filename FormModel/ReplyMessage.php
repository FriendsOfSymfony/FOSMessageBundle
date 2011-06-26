<?php

namespace Ornicar\MessageBundle\FormModel;

use Ornicar\MessageBundle\Model\ThreadInterface;

class ReplyMessage extends AbstractMessage
{
    /**
     * The thread we reply to
     *
     * @var ThreadInterface
     */
    protected $thread;

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
