<?php

namespace Ornicar\MessageBundle\Composer;

/**
 * Factory for message builders
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface ComposerInterface
{
    /**
     * Starts composing a message
     *
     * @return MessageBuilder
     */
    function compose();
}
