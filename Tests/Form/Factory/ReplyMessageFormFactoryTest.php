<?php

namespace FOS\MessageBundle\Tests\Form\Factory;

use FOS\MessageBundle\Form\Factory\ReplyMessageFormFactory;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Tests\Model\Thread;
use Mockery as m;

class ReplyMessageFormFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReplyMessageFormFactory
     */
    private $factory;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface|\Mockery\MockInterface
     */
    private $formFactory;

    /**
     * @var \Symfony\Component\Form\AbstractType|\Mockery\MockInterface
     */
    private $type;

    public function testCreate()
    {
        $thread = new Thread;

        $that = $this;
        $this->formFactory->shouldReceive('createNamed')
            ->with('formname', $this->type, m::on(function (MessageInterface $message) use ($that, $thread) {
                $that->assertSame($thread, $message->getThread());

                return true;
            }));

        $this->factory->create($thread);
    }

    protected function setUp()
    {
        $this->formFactory = m::mock('Symfony\\Component\\Form\\FormFactoryInterface');
        $this->type = m::mock('Symfony\\Component\\Form\\AbstractType');
        $this->factory = new ReplyMessageFormFactory(
            $this->formFactory,
            $this->type,
            'formname',
            'FOS\\MessageBundle\\Tests\\Model\\Message'
        );
    }
}
