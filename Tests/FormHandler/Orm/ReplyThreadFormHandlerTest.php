<?php

namespace FOS\MessageBundle\Tests\FormHandler\Orm;

use FOS\MessageBundle\FormHandler\Orm\ReplyThreadFormHandler;

/**
 * Test file for the replythreadformhandler
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class ReplyThreadFormHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $replyThreadFormHandler;
    private $actionsManager;
    private $replyBuilder;
    private $request;
    private $participantProvider;

    const MSG_BODY = 'body';

    public function setup()
    {
        $this->actionsManager = $this->getMock('FOS\MessageBundle\EntityManager\ActionsManagerInterface');
        $this->replyBuilder = $this->getMockBuilder('FOS\MessageBundle\MessageBuilder\Orm\ReplyBuilder')->disableOriginalConstructor()->getMock();
        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $this->participantProvider = $this->getMock('FOS\MessageBundle\Security\ParticipantProviderInterface');
        $this->replyThreadFormHandler = new ReplyThreadFormHandler($this->replyBuilder, $this->actionsManager, $this->request, $this->participantProvider);
    }

    public function testPersistThreadCallsActionManager()
    {
        $thread = $this->getMock('FOS\MessageBundle\Model\ThreadInterface');
        $this->actionsManager->expects($this->once())->method('addReply')->with($thread);
        $this->replyThreadFormHandler->persistThread($thread);
    }

    public function testCreateThreadObject()
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $replyMessage = $this->getMock('FOS\MessageBundle\FormModel\ReplyMessage');
        $form->expects($this->once())->method('getData')->will($this->returnValue($replyMessage));

        //thread
        $thread = $this->getMock('FOS\MessageBundle\Model\ThreadInterface');
        $replyMessage->expects($this->once())->method('getThread')->will($this->returnValue($thread));
        $this->replyBuilder->expects($this->once())->method('setThread')->with($thread);

        //body
        $replyMessage->expects($this->once())->method('getBody')->will($this->returnValue(self::MSG_BODY));
        $this->replyBuilder->expects($this->once())->method('setBody')->with(self::MSG_BODY);

        //sender
        $sender = $this->getMock('FOS\MessageBundle\Model\ParticipantInterface');
        $this->participantProvider->expects($this->once())->method('getAuthenticatedParticipant')->will($this->returnValue($sender));
        $this->replyBuilder->expects($this->once())->method('setSender')->with($sender);

        //date
        $this->replyBuilder->expects($this->once())->method('setCreatedAt');

        //call to the builder
        $threadBuild = $this->getMock('FOS\MessageBundle\Model\ThreadInterface');
        $this->replyBuilder->expects($this->once())->method('build')->will($this->returnValue($threadBuild));

        $this->assertEquals($threadBuild, $this->replyThreadFormHandler->createThreadObjectFromFormData($form));
    }
}
