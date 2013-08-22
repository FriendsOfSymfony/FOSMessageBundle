<?php
namespace FOS\MessageBundle\Sender;

use FOS\MessageBundle\Model\MessageInterface;

/**
 * Class SenderAggregate aggregates various senders into one
 * @package FOS\MessageBundle\Sender
 */
class SenderAggregate implements SenderInterface
{
    /**
     * @var \SplObjectStorage containing SenderInterface objects
     */
    protected $senders;

    public function __construct()
    {
        $this->senders = new \SplObjectStorage();
    }

    /**
     * @param SenderInterface $sender
     */
    public function add(SenderInterface $sender)
    {
        $this->senders->attach($sender);
    }

    /**
     * @param SenderInterface $sender
     */
    public function remove(SenderInterface $sender)
    {
        $this->senders->detach($sender);
    }

    /**
     * @param MessageInterface $message
     */
    public function send(MessageInterface $message)
    {
        foreach ($this->senders as $sender) {
            $sender->send($message);
        }
    }
}

