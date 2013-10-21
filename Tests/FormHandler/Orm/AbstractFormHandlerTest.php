<?php

namespace FOS\MessageBundle\Tests\FormHandler\Orm;

/**
 * Test file for abstract class FOS\MessageBundle\FormHandler\Orm\AbstractFormHandler
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class AbstractFormHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $formHandler;
    private $request;
    private $participantProvider;
    private $form;

    public function setUp()
    {
        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();
        $this->participantProvider = $this->getMock('FOS\MessageBundle\Security\ParticipantProviderInterface');
        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $this->formHandler = $this->getMockForAbstractClass('FOS\MessageBundle\FormHandler\Orm\AbstractFormHandler', array($this->request, $this->participantProvider));
    }

    public function testProcessReturnsFalseWhenNotSubmittedForm()
    {
        $this->request->expects($this->once())->method('getMethod')->will($this->returnValue('GET'));
        $this->form->expects($this->never())->method('bind');
        $this->expectsNotCreatingThreadObject();
        $this->assertFalse($this->formHandler->process($this->form));
    }

    public function testProcessWithSubmittedFormBindsTheForm()
    {
        $this->expectsRequestMethodPost();
        $this->expectsBindingFormWithRequest();
        $this->expectsNotCreatingThreadObject();
        $this->formHandler->process($this->form);
    }

    public function testProcessWithSubmittedFormValidatesTheForm()
    {
        $this->expectsRequestMethodPost();
        $this->expectsBindingFormWithRequest();
        $this->expectsNotCreatingThreadObject();
        $this->form->expects($this->once())->method('isValid');
        $this->formHandler->process($this->form);
    }

    public function testProcessWithSubmittedFormReturnsFalseWhenInvalidForm()
    {
        $this->expectsRequestMethodPost();
        $this->expectsBindingFormWithRequest();
        $this->form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $this->expectsNotCreatingThreadObject();
        $this->assertFalse($this->formHandler->process($this->form));
    }

    public function testProcessWithValidForm()
    {
        $this->expectsRequestMethodPost();
        $this->expectsBindingFormWithRequest();
        $this->form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $createdThread = $this->getMock('FOS\MessageBundle\Model\ThreadInterface');
        $this->formHandler->expects($this->once())
            ->method('createThreadObjectFromFormData')
            ->with($this->form)
            ->will($this->returnValue($createdThread));

        $this->formHandler->expects($this->once())->method('persistThread')->with($createdThread);
        $lastMessage = $this->getMock('FOS\MessageBundle\Model\MessageInterface');
        $createdThread->expects($this->once())->method('getLastMessage')->will($this->returnValue($lastMessage));

        $this->assertEquals($lastMessage, $this->formHandler->process($this->form));
    }

    protected function expectsRequestMethodPost()
    {
       $this->request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));
    }

    protected function expectsBindingFormWithRequest()
    {
       $this->form->expects($this->once())->method('bind')->with($this->request);
    }

    protected function expectsNotCreatingthreadObject()
    {
        $this->formHandler->expects($this->never())
            ->method('createThreadObjectFromFormData');

        $this->formHandler->expects($this->never())
            ->method('persistThread');
    }
}
