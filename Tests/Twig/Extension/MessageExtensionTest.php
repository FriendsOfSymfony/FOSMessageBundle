<?php

namespace FOS\MessageBundle\Tests\Twig\Extension;

use FOS\MessageBundle\Twig\Extension\MessageExtension;

/**
 * Testfile for MessageExtension
 */
class MessageExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;
    private $participantProvider;   
    private $provider;
    private $authorizer;
    private $participant;
    
    public function setUp() 
    {        
        $this->participantProvider = $this->getMock('FOS\MessageBundle\Security\ParticipantProviderInterface');
        $this->provider = $this->getMock('FOS\MessageBundle\Provider\ProviderInterface');
        $this->authorizer = $this->getMock('FOS\MessageBundle\Security\AuthorizerInterface');        
        $this->participant = $this->getMock('FOS\MessageBundle\Model\ParticipantInterface');        
        $this->extension = new MessageExtension($this->participantProvider, $this->provider, $this->authorizer);
    }
    
    public function testIsReadReturnsTrueWhenRead() 
    {
        $this->participantProvider->expects($this->once())->method('getAuthenticatedParticipant')->will($this->returnValue($this->participant));
        $readAble = $this->getMock('FOS\MessageBundle\Model\ReadableInterface');
        $readAble->expects($this->once())->method('isReadByParticipant')->with($this->participant)->will($this->returnValue(true));        
        $this->assertTrue($this->extension->isRead($readAble));
    }
    
    public function testIsReadReturnsFalseWhenNotRead() 
    {
        $this->participantProvider->expects($this->once())->method('getAuthenticatedParticipant')->will($this->returnValue($this->participant));
        $readAble = $this->getMock('FOS\MessageBundle\Model\ReadableInterface');
        $readAble->expects($this->once())->method('isReadByParticipant')->with($this->participant)->will($this->returnValue(false));        
        $this->assertFalse($this->extension->isRead($readAble));
    }
    
    public function testCanDeleteThreadWhenHasPermission() 
    {
        $thread = $this->getThreadMock();
        $this->authorizer->expects($this->once())->method('canDeleteThread')->with($thread)->will($this->returnValue(true));
        $this->assertTrue($this->extension->canDeleteThread($thread));    
    }
    
    public function testCanDeleteThreadWhenNoPermission() 
    {
        $thread = $this->getThreadMock();
        $this->authorizer->expects($this->once())->method('canDeleteThread')->with($thread)->will($this->returnValue(false));
        $this->assertFalse($this->extension->canDeleteThread($thread));    
    }
    
    public function testIsThreadDeletedByParticipantWhenDeleted() 
    {
        $thread = $this->getThreadMock();
        $this->participantProvider->expects($this->once())->method('getAuthenticatedParticipant')->will($this->returnValue($this->participant));
        $thread->expects($this->once())->method('isDeletedByParticipant')->with($this->participant)->will($this->returnValue(true));
        $this->assertTrue($this->extension->isThreadDeletedByParticipant($thread));       
    }
    
    public function testGetNbUnreadCacheStartsEmpty() 
    {        
        $this->assertAttributeEmpty('nbUnreadMessagesCache', $this->extension);
        $this->extension->getNbUnread();
    }
    
    public function testGetNbUnread() 
    {
        $this->assertAttributeEmpty('nbUnreadMessagesCache', $this->extension);
        $this->provider->expects($this->once())->method('getNbUnreadMessages')->will($this->returnValue(3));
        $this->assertEquals(3, $this->extension->getNbUnread());
    }
    
    public function testGetNbUnreadStoresCache() 
    {
        $this->provider->expects($this->once())->method('getNbUnreadMessages')->will($this->returnValue(3));
        //we call it twice but expect to only get one call
        $this->extension->getNbUnread();
        $this->extension->getNbUnread();
    }
    
    public function testGetName() 
    {
        $this->assertEquals('fos_message' , $this->extension->getName());
    }
    
    protected function getThreadMock() 
    {
        return $this->getMock('FOS\MessageBundle\Model\ThreadInterface');
    }
}
