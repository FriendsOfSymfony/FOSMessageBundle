<?php

namespace FOS\MessageBundle\Tests\Composer;

use FOS\MessageBundle\Composer\Composer;
use FOS\MessageBundle\Tests\Entity\Message;
use FOS\MessageBundle\Tests\Entity\Thread;
use Mockery as m;

class ComposerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var \FOS\MessageBundle\ModelManager\MessageManagerInterface|\Mockery\Mock
     */
    private $messageManager;

    /**
     * @var \FOS\MessageBundle\ModelManager\ThreadManagerInterface|\Mockery\Mock
     */
    private $threadManager;

    public function testNewThread()
    {
        $this->messageManager->shouldReceive('createMessage')
            ->once()
            ->andReturn($message = new Message);
        $this->threadManager->shouldReceive('createThread')
            ->once()
            ->andReturn($thread = new Thread);

        $builder = $this->composer->newThread();

        $this->assertInstanceOf('FOS\\MessageBundle\\MessageBuilder\\NewThreadMessageBuilder', $builder);
        $this->assertSame($message, $builder->getMessage());
        $this->assertSame($thread, $builder->getThread());
    }

    public function testReply()
    {
        $thread = new Thread;
        $this->messageManager->shouldReceive('createMessage')
            ->once()
            ->andReturn($message = new Message);

        $builder = $this->composer->reply($thread);

        $this->assertInstanceOf('FOS\\MessageBundle\\MessageBuilder\\ReplyMessageBuilder', $builder);
        $this->assertSame($message, $builder->getMessage());
        $this->assertSame($thread, $builder->getThread());
    }

    protected function setUp()
    {
        $this->messageManager = m::mock('FOS\\MessageBundle\\ModelManager\\MessageManagerInterface');
        $this->threadManager = m::mock('FOS\\MessageBundle\\ModelManager\\ThreadManagerInterface');

        $this->composer = new Composer($this->messageManager, $this->threadManager);
    }

}
