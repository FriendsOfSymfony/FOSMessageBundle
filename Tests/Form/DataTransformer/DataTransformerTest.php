<?php

namespace FOS\MessageBundle\Tests\Form\DataTransformer;

use FOS\MessageBundle\Form\DataTransformer\RecipientsDataTransformer;
use FOS\UserBundle\Form\DataTransformer\UserToUsernameTransformer;
use Mockery as m;

class DataTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RecipientsDataTransformer
     */
    private $transformer;

    /**
     * @var \FOS\UserBundle\Form\DataTransformer\UserToUsernameTransformer
     */
    private $userTransformer;

    /**
     * @var \FOS\UserBundle\Model\UserManagerInterface|\Mockery\MockInterface
     */
    private $userManager;

    public function testTransformNull()
    {
        $result = $this->transformer->transform(null);

        $this->assertEquals('', $result);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testTransformInvalidType()
    {
        $this->transformer->transform('string');
    }

    public function testTransformUsersToString()
    {
        $result = $this->transformer->transform(array(
            $this->getUser('Tim'),
            $this->getUser('Bill')
        ));

        $this->assertEquals('Tim, Bill', $result);
    }

    public function testTransformSingleUserToString()
    {
        $result = $this->transformer->transform(array(
            $this->getUser('Tim'),
        ));

        $this->assertEquals('Tim', $result);
    }

    public function testTransformNoUsersToString()
    {
        $result = $this->transformer->transform(array());

        $this->assertEquals('', $result);
    }

    public function testTransformStringToUsers()
    {
        $this->userManager->shouldReceive('findUserByUsername')
            ->with('Tim')
            ->andReturn($this->getUser('Tim'))
            ->once();
        $this->userManager->shouldReceive('findUserByUsername')
            ->with('Bill')
            ->andReturn($this->getUser('Bill'))
            ->once();

        $result = $this->transformer->reverseTransform('Tim, Bill');

        $this->assertCount(2, $result);
        $this->assertEquals('Tim', $result[0]->getUsername());
        $this->assertEquals('Bill', $result[1]->getUsername());
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function testTransformStringToUsersWithNonExistantUser()
    {
        $this->userManager->shouldReceive('findUserByUsername')
            ->with('Tim')
            ->andReturn($this->getUser('Tim'))
            ->once();
        $this->userManager->shouldReceive('findUserByUsername')
            ->with('Bill')
            ->andReturnNull()
            ->once();

        $this->transformer->reverseTransform('Tim, Bill');
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testReverseTransformInvalid()
    {
        $this->transformer->reverseTransform(array());
    }

    public function testReverseTransformEmpty()
    {
        $result = $this->transformer->reverseTransform(null);

        $this->assertNull($result);

        $result = $this->transformer->reverseTransform('');

        $this->assertNull($result);
    }

    protected function setUp()
    {
        $this->userManager = m::mock('FOS\\UserBundle\\Model\\UserManagerInterface');
        $this->userTransformer = new UserToUsernameTransformer($this->userManager);
        $this->transformer = new RecipientsDataTransformer($this->userTransformer);
    }

    public function getUser($username)
    {
        $user = m::mock('FOS\\UserBundle\\Model\\UserInterface');
        $user->shouldReceive('getUsername')
            ->andReturn($username);

        return $user;
    }
}
