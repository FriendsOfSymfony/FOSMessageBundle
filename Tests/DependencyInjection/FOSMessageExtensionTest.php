<?php

namespace FOS\MessageBundle\Tests\DependencyInjection;

use FOS\MessageBundle\DependencyInjection\FOSMessageExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class FOSMessageExtensionTest extends \PHPUnit_Framework_TestCase
{

    /** @var ContainerBuilder */
    protected $configuration;

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
        $this->configuration = new ContainerBuilder();
        $loader = new FOSMessageExtension();
        $config = $this->getEmptyConfigWithFlashesEnabled();
        $loader->load(array($config), $this->configuration);

        $this->assertHasDefinition('fos_message.flash_listener');
        $this->assertParameter('success', 'fos_message.flash_messages_key');
    }

    protected function createEmptyConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSMessageExtension();
        $config = $this->getEmptyConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * getEmptyConfig
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
     * @param mixed $value
     * @param string $key
     */
    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    /**
     * @param string $id
     */
    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ? : $this->configuration->hasAlias($id)));
    }

    /**
     * @param string $id
     */
    private function assertNotHasDefinition($id)
    {
        $this->assertFalse(($this->configuration->hasDefinition($id) ? : $this->configuration->hasAlias($id)));
    }
}
