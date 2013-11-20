<?php

namespace FOS\MessageBundle\MessageBuilder\Orm\Examples;

use FOS\MessageBundle\MessageBuilder\Orm\NewThreadBuilder;
use FOS\MessageBundle\Model\ThreadInterface;

/**
 * This is an example of how to extend this class
 *
 * The boss says:
 * If my wife sends me a message I want it automaticly marked as deleted
 * Ow and make sure you don't screw this up I want to see tests for this!!!
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class NoWifeMsgForBossNewThreadBuilder extends NewThreadBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $thread = parent::build();

        $sender = $thread->getCreatedBy();

        //the sender is not the wife, make sure to return the thread
        if ($sender->getId() !== 'wife') {
            return $thread;
        }

        //sender is wife we need to see if she bugs the boss
        $recipients = $thread->getOtherParticipants($sender);
        foreach ($recipients as $recipient) {
            if ($recipient->getid() === "boss") {
                //she bugged the boss let's do our magic and return the thread no more work needed
                return $thread = $this->noWifeMsgForBoss($thread, $recipient);
            }
        }
        //wife is bugging other people
        return $thread;
    }

    protected function noWifeMsgForBoss(ThreadInterface $thread, $boss)
    {
        $bossThreadMeta = $thread->getMetadataForParticipant($boss);
        $bossThreadMeta->setIsDeleted(true);

        return $thread;
    }
}
