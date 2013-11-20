<?php

namespace FOS\MessageBundle\Tests\MessageBuilder\Orm;

use FOS\MessageBundle\MessageBuilder\Orm\AbstractMessageBuilder;

/**
 * Test file for the abstract message builder public functions
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class AbstractMessageBuilderTest extends OrmBuilderTestHelpers
{
    /**
     * The class under test
     * 
     * @var AbstractMessageBuilder
     */
    protected $abstractMessageBuilder;

    public function setUp()
    {
        $this->abstractMessageBuilder = $this->getMockForAbstractClass('FOS\MessageBundle\MessageBuilder\Orm\AbstractMessageBuilder');
    }

    public function testSetSenderWorks()
    {
        $sender = $this->getSender();
        $this->abstractMessageBuilder->setSender($sender);
        $this->assertAttributeEquals($sender, 'sender', $this->abstractMessageBuilder);
    }

    public function testSetBodyWorks()
    {
        $body = 'message body';
        $this->abstractMessageBuilder->setBody($body);
        $this->assertAttributeEquals($body, 'body', $this->abstractMessageBuilder);
    }

    public function testSetCreatedAtWorks()
    {
        $createdAt = new \DateTime('2013-10-09 23:22:11');
        $this->abstractMessageBuilder->setCreatedAt($createdAt);
        $this->assertAttributeEquals($createdAt, 'createdAt', $this->abstractMessageBuilder);
    }
}
