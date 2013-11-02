<?php

namespace FOS\MessageBundle\Composer;

use FOS\MessageBundle\Model\ThreadInterface;

/**
 * Factory for message builders
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface ComposerInterface
{
    /**
     * Starts composing a message, starting a new thread
     *
     * @return \FOS\MessageBundle\MessageBuilder\NewThreadMessageBuilder
     */
    public function newThread();

    /**
     * Starts composing a message in a reply to a thread
     *
     * @param ThreadInterface $thread
     * @return \FOS\MessageBundle\MessageBuilder\ReplyMessageBuilder
     */
    public function reply(ThreadInterface $thread);
}
