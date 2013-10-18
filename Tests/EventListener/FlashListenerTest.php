<?php
namespace FOS\MessageBundle\Tests\EventListener;

use FOS\MessageBundle\EventListener\FlashListener;
use FOS\MessageBundle\Event\FOSMessageEvents;

/**
 * Test for the Flash listener
 * 
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class FlashListenerTest extends \PHPUnit_Framework_TestCase
{
    private $listener;
    private $session;
    private $translator;
    private $event;   
    private $key;
    
    public function setUp() 
    {
        $this->event = $this->getMock('Symfony\Component\EventDispatcher\Event');
        
        //if we use the interface getflashbag returns an error...
        $this->session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $this->key = 'success';
        $this->listener = new FlashListener($this->session, $this->translator, $this->key);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage This event does not correspond to a known flash message
     */
    public function testAddFlashWithNonSupportedEvent() 
    {
        $this->event->expects($this->once())->method('getName')->will($this->returnValue('foo'));
        $this->listener->addSuccessFlash($this->event);        
    }
    
    public function testAddFlashOnValidEventWithDefaultKey()
    {
        $flashbagMock = $this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        
        $this->event->expects($this->once())->method('getName')->will($this->returnValue(FOSMessageEvents::POST_SEND));
        
        $this->translator->expects($this->once())
            ->method('trans')
            ->with('flash_post_send_success', array(), 'FOSMessageBundle')
            ->will($this->returnValue('translatedString'));
        
        $this->session->expects($this->once())
            ->method('getFlashBag')
            ->will($this->returnValue($flashbagMock));
        
        $flashbagMock->expects($this->once())
            ->method('add')
            ->with('success', 'translatedString');
        
        $this->listener->addSuccessFlash($this->event);
    }
}
