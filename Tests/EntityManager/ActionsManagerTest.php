<?php

namespace FOS\MessageBundle\Tests\EntityManager;

use FOS\MessageBundle\EntityManager\ActionsManager;

/**
 * Test file for the actionsmanager
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class ActionsManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $actionsManager;
    protected $messageManager;
    protected $threadManager;

    public function setUp()
    {
        $this->messageManager = $this->getMock('FOS\MessageBundle\ModelManager\MessageManagerInterface');
        $this->threadManager = $this->getMock('FOS\MessageBundle\ModelManager\ThreadManagerInterface');
        $this->actionsManager = new ActionsManager($this->messageManager, $this->threadManager);
    }

    public function testAddThread()
    {
        $thread = $this->getmock('FOS\MessageBundle\Model\ThreadInterface');
        $message = $this->getmock('FOS\MessageBundle\Model\MessageInterface');
        $thread->expects($this->once())->method('getLastMessage')->will($this->returnValue($message));
        $this->threadManager->expects($this->once())->method('saveThread')->with($thread, true);
        $this->messageManager->expects($this->once())->method('saveMessage')->with($message, false);
        $this->actionsManager->addThread($thread);
    }

    public function testAddReply()
    {
        $thread = $this->getmock('FOS\MessageBundle\Model\ThreadInterface');
        $message = $this->getmock('FOS\MessageBundle\Model\MessageInterface');
        $thread->expects($this->once())->method('getLastMessage')->will($this->returnValue($message));
        $this->threadManager->expects($this->once())->method('saveThread')->with($thread, true);
        $this->messageManager->expects($this->once())->method('saveMessage')->with($message, false);
        $this->actionsManager->addReply($thread);
    }

}
