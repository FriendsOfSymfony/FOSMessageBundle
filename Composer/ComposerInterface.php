<?php

namespace FOS\MessageBundle\Composer;

use FOS\MessageBundle\MessageBuilder\AbstractMessageBuilder;
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
     * @return AbstractMessageBuilder
     */
    public function newThread();

    /**
     * Starts composing a message in a reply to a thread
     *
     * @return AbstractMessageBuilder
     */
    public function reply(ThreadInterface $thread);
}
