<?php

/*
 * This file is part of the FOSMessageBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use FOS\MessageBundle\DependencyInjection\FOSMessageExtension;
use Symfony\Component\Yaml\Parser;

class FOSMessageExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerBuilder */
    protected $configuration;

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testMessageLoadThrowsExceptionUnlessDatabaseDriverSet()
    {
        $loader = new FOSMessageExtension();
        $config = $this->getEmptyConfig();
        unset($config['db_driver']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testMessageLoadThrowsExceptionUnlessDatabaseDriverIsInValid()
    {
        $loader = new FOSMessageExtension();
        $config = $this->getEmptyConfig();
        $config['db_driver'] = 'foo';
        $loader->load(array($config), new ContainerBuilder());
    }

    public function testExtensionSetsModelParameters()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('FOS\\MessageBundle\\Tests\\Model\\Message', 'fos_message.message_class');
        $this->assertParameter('FOS\\MessageBundle\\Tests\\Model\\Thread', 'fos_message.thread_class');

        $this->assertAlias('fos_message.message_manager.default', 'fos_message.message_manager');
        $this->assertAlias('fos_message.thread_manager.default', 'fos_message.thread_manager');
    }

    public function testExtensionSetsNewThreadForm()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('FOS\\MessageBundle\\Form\\Model\\NewThreadMessage', 'fos_message.new_thread_form.model');
        $this->assertParameter('message', 'fos_message.new_thread_form.name');

        $this->assertAlias('fos_message.new_thread_form.factory.default', 'fos_message.new_thread_form.factory');
        $this->assertAlias('fos_message.new_thread_form.handler.default', 'fos_message.new_thread_form.handler');
        $this->assertAlias('fos_message.new_thread_form.type.default', 'fos_message.new_thread_form.type');
    }

    public function testExtensionSetsReplyForm()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('FOS\\MessageBundle\\Form\\Model\\ReplyMessage', 'fos_message.reply_form.model');
        $this->assertParameter('message', 'fos_message.reply_form.name');

        $this->assertAlias('fos_message.reply_form.factory.default', 'fos_message.reply_form.factory');
        $this->assertAlias('fos_message.reply_form.handler.default', 'fos_message.reply_form.handler');
        $this->assertAlias('fos_message.reply_form.type.default', 'fos_message.reply_form.type');
    }

    public function testExtensionSetsArguments()
    {
        $this->createEmptyConfiguration();

        $searchArgument = $this->configuration->getDefinition('fos_message.search_query_factory.default')
            ->getArgument(1);
        $this->assertEquals('q', $searchArgument);

        $userTransformer = $this->configuration->getDefinition('fos_message.recipients_data_transformer')
            ->getArgument(0);
        $this->assertEquals('fos_user.user_to_username_transformer', $userTransformer);
    }

    public function testMiscAliases()
    {
        $this->createEmptyConfiguration();

        $this->assertAlias('fos_message.authorizer.default', 'fos_message.authorizer');
        $this->assertAlias('fos_message.composer.default', 'fos_message.composer');
        $this->assertAlias('fos_message.deleter.default', 'fos_message.deleter');
        $this->assertAlias('fos_message.message_reader.default', 'fos_message.message_reader');
        $this->assertAlias('fos_message.noop_spam_detector', 'fos_message.spam_detector');
        $this->assertAlias('fos_message.participant_provider.default', 'fos_message.participant_provider');
        $this->assertAlias('fos_message.provider.default', 'fos_message.provider');
        $this->assertAlias('fos_message.sender.default', 'fos_message.sender');
        $this->assertAlias('fos_message.thread_reader.default', 'fos_message.thread_reader');
        $this->assertAlias('fos_message.twig_extension.default', 'fos_message.twig_extension');
    }

    protected function createEmptyConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSMessageExtension();
        $config = $this->getEmptyConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    protected function createFullConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSMessageExtension();
        $config = $this->getFullConfig();
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
db_driver: orm
thread_class: FOS\MessageBundle\Tests\Model\Thread
message_class: FOS\MessageBundle\Tests\Model\Message
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    protected function getFullConfig()
    {
        $yaml = <<<EOF
db_driver: foo
thread_class: FOS\MessageBundle\Tests\Model\Thread
message_class: FOS\MessageBundle\Tests\Model\Message
message_manager: fos_message.message_manager.default
thread_manager: fos_message.thread_manager.default
sender: fos_message.sender.default
composer: fos_message.composer.default
provider: fos_message.provider.default
participant_provider: fos_message.participant_provider.default
authorizer: fos_message.authorizer.default
message_reader: fos_message.message_reader.default
thread_reader: fos_message.thread_reader.default
deleter: fos_message.deleter.default
spam_detector: fos_message.noop_spam_detector
twig_extension: fos_message.twig_extension.default
user_transformer: fos_user.user_to_username_transformer
search:
    query_factory: fos_message.search_query_factory.default
    finder: fos_message.search_finder.default
    query_parameter: q
new_thread_form:
    factory: fos_message.new_thread_form.factory.default
    type: fos_message.new_thread_form.type.default
    handler: fos_message.new_thread_form.handler.default
    name: message
    model: FOS\MessageBundle\Form\Model\NewThreadMessage
reply_form:
    factory: fos_message.reply_form.factory.default
    type: fos_message.reply_form.type.default
    handler: fos_message.reply_form.handler.default
    name: message
    model: FOS\MessageBundle\Form\Model\ReplyMessage
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * @param string $value
     * @param string $key
     */
    private function assertAlias($value, $key)
    {
        $this->assertEquals($value, (string) $this->configuration->getAlias($key), sprintf('%s alias is correct', $key));
    }

    /**
     * @param mixed  $value
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
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    /**
     * @param string $id
     */
    private function assertNotHasDefinition($id)
    {
        $this->assertFalse(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    protected function tearDown()
    {
        unset($this->configuration);
    }
}