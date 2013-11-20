<?php

namespace FOS\MessageBundle\Tests\FormHandler\Orm;

use FOS\MessageBundle\FormHandler\Orm\NewThreadFormHandler;

/**
 * Test file for the NewThreadFormHandler class
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class NewThreadFormHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $newThreadBuilder;
    private $newThreadFormHandler;
    private $actionsManager;
    private $request;
    private $participantProvider;

    const MSG_SUBJECT = "subject";
    const MSG_BODY = "this is a body";

    public function setUp()
    {
        $this->newThreadBuilder = $this->getMockBuilder('FOS\MessageBundle\MessageBuilder\Orm\NewThreadBuilder')->disableOriginalConstructor()->getMock();
        $this->actionsManager = $this->getMock('FOS\MessageBundle\EntityManager\ActionsManagerInterface');
        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $this->participantProvider = $this->getMock('FOS\MessageBundle\Security\ParticipantProviderInterface');
        $this->newThreadFormHandler = new NewThreadFormHandler($this->newThreadBuilder, $this->actionsManager, $this->request, $this->participantProvider);
    }

    public function testPersistThreadCallsActionManager()
    {
        $thread = $this->getMock('FOS\MessageBundle\Model\ThreadInterface');
        $this->actionsManager->expects($this->once())->method('addThread')->with($thread);
        $this->newThreadFormHandler->persistThread($thread);
    }

    public function testCreateThreadObject()
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $newThreadMessage = $this->getMock('FOS\MessageBundle\FormModel\NewThreadMessage');
        $form->expects($this->once())->method('getData')->will($this->returnValue($newThreadMessage));

        //date
        $this->newThreadBuilder->expects($this->once())->method('setCreatedAt');

        //subject
        $newThreadMessage->expects($this->once())->method('getSubject')->will($this->returnValue(self::MSG_SUBJECT));
        $this->newThreadBuilder->expects($this->once())->method('setSubject')->with(self::MSG_SUBJECT);

        //body
        $newThreadMessage->expects($this->once())->method('getBody')->will($this->returnValue(self::MSG_BODY));
        $this->newThreadBuilder->expects($this->once())->method('setBody')->with(self::MSG_BODY);

        //recipients
        $recipient = $this->getMock('FOS\MessageBundle\Model\ParticipantInterface');
        $newThreadMessage->expects($this->once())->method('getRecipient')->will($this->returnValue($recipient));
        $this->newThreadBuilder->expects($this->once())->method('setRecipients')->with(array($recipient));

        //sender
        $sender = $this->getMock('FOS\MessageBundle\Model\ParticipantInterface');
        $this->participantProvider->expects($this->once())->method('getAuthenticatedParticipant')->will($this->returnValue($sender));
        $this->newThreadBuilder->expects($this->once())->method('setSender')->with($sender);

        //spam
        $this->newThreadBuilder->expects($this->once())->method('setIsSpam')->with(false);

        //call to the builder
        $thread = $this->getMock('FOS\MessageBundle\Model\ThreadInterface');
        $this->newThreadBuilder->expects($this->once())->method('build')->will($this->returnValue($thread));

        $this->assertEquals($thread, $this->newThreadFormHandler->createThreadObjectFromFormData($form));
    }
}
