<?php

namespace FOS\MessageBundle\Tests\DependencyInjection;

use FOS\MessageBundle\DependencyInjection\FOSMessageExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class FOSMessageExtensionTest extends \PHPUnit_Framework_TestCase
{

    /** @var ContainerBuilder */
    protected $containerBuilder;

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testMessageLoadThrowsExceptionUnlessDbDriverSet()
    {
        $loader = new FOSMessageExtension();
        $config = $this->getEmptyConfig();
        unset($config['db_driver']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testMessageLoadThrowsExceptionUnlessThreadClassSet()
    {
        $loader = new FOSMessageExtension();
        $config = $this->getEmptyConfig();
        unset($config['thread_class']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testMessageLoadThrowsExceptionUnlessMessageClassSet()
    {
        $loader = new FOSMessageExtension();
        $config = $this->getEmptyConfig();
        unset($config['message_class']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid db driver "foo"
     */
    public function testUnsupportedDbDriverThrowsException()
    {
        $loader = new FOSMessageExtension();
        $config = $this->getEmptyConfig();
        $config['db_driver'] = "foo";
        $loader->load(array($config), new ContainerBuilder());
    }

    public function testFlashesAreDisabledByDefault()
    {
        $this->createEmptyConfiguration();
        $this->assertNotHasDefinition('fos_message.flash_listener');
    }

    public function testUserEnablesFlashes()
    {
        $this->containerBuilder = new ContainerBuilder();
        $loader = new FOSMessageExtension();
        $config = $this->getEmptyConfigWithFlashesEnabled();
        $loader->load(array($config), $this->containerBuilder);

        $this->assertHasDefinition('fos_message.flash_listener');
        $this->assertParameter('success', 'fos_message.flash_messages_key');
    }

    protected function createEmptyConfiguration()
    {
        $this->containerBuilder = new ContainerBuilder();
        $loader = new FOSMessageExtension();
        $config = $this->getEmptyConfig();
        $loader->load(array($config), $this->containerBuilder);
        $this->assertTrue($this->containerBuilder instanceof ContainerBuilder);
    }

    /**
     * gets an empty config
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
db_driver: mongodb
thread_class: Acme\MyBundle\Entity\Thread
message_class: Acme\MyBundle\Entity\Message
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * Gets an empty config but with the optional flash settings enabled
     * 
     * @return array
     */
    protected function getEmptyConfigWithFlashesEnabled()
    {
       $yaml = <<<EOF
db_driver: mongodb
thread_class: Acme\MyBundle\Entity\Thread
message_class: Acme\MyBundle\Entity\Message
flash_messages:
    enabled: true
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * Asserts that a parameter key has a certain value
     * 
     * @param mixed  $value
     * @param string $key
     */
    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->containerBuilder->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    /**
     * Asserts that a definition exists
     * 
     * @param string $id
     */
    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->containerBuilder->hasDefinition($id) || $this->containerBuilder->hasAlias($id)));
    }

    /**
     * Asserts that a definition does not exist 
     * 
     * @param string $id
     */
    private function assertNotHasDefinition($id)
    {
        $this->assertFalse(($this->containerBuilder->hasDefinition($id) || $this->containerBuilder->hasAlias($id)));
    }
}
