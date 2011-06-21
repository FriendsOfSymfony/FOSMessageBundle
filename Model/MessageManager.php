<?php

namespace Ornicar\MessageBundle\Model;

/**
 * Abstract Message Manager implementation which can be used as base by
 * your concrete manager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class UserManager implements MessageManagerInterface
{
    /**
     * Creates an empty message instance, bound to a thread
     *
     * @param ThreadInterface $thread
     * @return MessageInterface
     */
    public function createMessage(ThreadInterface $thread)
    {
        $class = $this->getClass();
        $message = new $class;
        $message->setThread($thread);

        return $message;
    }
}
