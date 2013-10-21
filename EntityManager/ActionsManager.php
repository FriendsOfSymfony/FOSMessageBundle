<?php

namespace FOS\MessageBundle\EntityManager;

use FOS\MessageBundle\ModelManager\MessageManagerInterface;
use FOS\MessageBundle\ModelManager\ThreadManagerInterface;
use FOS\MessageBundle\Model\ThreadInterface;

/**
 * This class implements the facade pattern
 *
 * It decouples the fact that we need both managers to succesfully add a threat or a reply
 * 
 * @todo Also add the events here
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class ActionsManager implements ActionsManagerInterface
{
    /**
     * A message manager instance
     *
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * A thread manager instance
     *
     * @var ThreadManagerInterface
     */
    protected $threadManager;

    /**
     * Constructor.
     *
     * @param MessageManagerInterface $messageManager A message manager instance
     * @param ThreadManagerInterface  $threadManager  A thread manager instance
     */
    public function __construct(MessageManagerInterface $messageManager, ThreadManagerInterface $threadManager)
    {
        $this->messageManager = $messageManager;
        $this->threadManager = $threadManager;
    }

    /**
     * {@inheritdoc}
     */
    public function addThread(ThreadInterface $thread)
    {
        $message = $thread->getLastMessage();
        $this->messageManager->saveMessage($message, false);
        $this->threadManager->saveThread($thread, true);
    }

    /**
     * {@inheritdoc}
     */
    public function addReply(ThreadInterface $thread)
    {
        $message = $thread->getLastMessage();
        $this->messageManager->saveMessage($message, false);
        $this->threadManager->saveThread($thread, true);
    }
}
