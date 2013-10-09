<?php

namespace FOS\MessageBundle\Tests\Entity;

use FOS\MessageBundle\Tests\Entity\Thread;
use Mockery as m;

class ThreadTest extends \PHPUnit_Framework_TestCase
{
    public function testAddMetadata()
    {
        $thread = new Thread;

        $metadata = m::mock('FOS\\MessageBundle\\Model\\ThreadMetadata');
        $metadata->shouldReceive('setThread')
            ->with($thread)
            ->once();

        $thread->addMetadata($metadata);
    }
}
