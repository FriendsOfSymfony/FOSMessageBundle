<?php

namespace FOS\MessageBundle\Tests\Form\Factory;

use FOS\MessageBundle\Form\Factory\NewThreadMessageFormFactory;
use Mockery as m;

class NewThreadMessageFormFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NewThreadMessageFormFactory
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
        $this->formFactory->shouldReceive('createNamed')
            ->with('formname', $this->type, m::type('FOS\\MessageBundle\\Tests\\Model\\Message'));

        $this->factory->create();
    }

    protected function setUp()
    {
        $this->formFactory = m::mock('Symfony\\Component\\Form\\FormFactoryInterface');
        $this->type = m::mock('Symfony\\Component\\Form\\AbstractType');
        $this->factory = new NewThreadMessageFormFactory(
            $this->formFactory,
            $this->type,
            'formname',
            'FOS\\MessageBundle\\Tests\\Model\\Message'
        );
    }
}
