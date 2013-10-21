<?php

namespace FOS\MessageBundle\MessageBuilder\Orm\Examples;

use FOS\MessageBundle\Tests\MessageBuilder\Orm\OrmBuilderTestHelpers;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\MessageBuilder\Orm\Examples\NoWifeMsgForBossNewThreadBuilder;

/**
 * This is the testfile for the no wife messages for the boss
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class NoWifeMsgForBossNewThreadBuilderTest extends OrmBuilderTestHelpers
{
    /**
     * NoWifeMsgForBossNewThreadBuilder
     * 
     * @var NoWifeMsgForBossNewThreadBuilder
     */
    protected $builder;

    public function setUp()
    {
        $class = $this->getClasses();

        $this->builder = new NoWifeMsgForBossNewThreadBuilder;
        $this->builder->setMessageClass($class['message']);
        $this->builder->setThreadClass($class['thread']);
        $this->builder->setMessageMetaClass($class['messageMeta']);
        $this->builder->setThreadMetaClass($class['threadMeta']);
    }

    public function testWifeMessagesBossSetsThreadAsDeletedForBossOnlyAndReturnsThread()
    {
        $wife = new Wife();
        $boss = new Boss();
        $otherRecipient = new OtherRecipient;

        $this->builder->setCreatedAt(new \DateTime('now'));
        $this->builder->setSubject('I love you both sweetiees!!');
        $this->builder->setBody('xxxx');
        $this->builder->setSender($wife);
        $this->builder->setRecipients(array($boss, $otherRecipient));
        $thread = $this->builder->build();
        $this->assertInstanceOf('FOS\MessageBundle\Model\ThreadInterface', $thread);
        $this->assertTrue($thread->getMetadataForParticipant($boss)->getIsDeleted());
        $this->assertFalse($thread->getMetadataForParticipant($otherRecipient)->getIsDeleted());
        $this->assertFalse($thread->getMetadataForParticipant($wife)->getIsDeleted());
    }

    public function testWifeCanStillBugOthers()
    {
        $wife = new wife();
        $other = new OtherRecipient();
        $this->builder->setCreatedAt(new \DateTime('now'));
        $this->builder->setSubject('Where are you??');
        $this->builder->setBody('Cook me dinner!!');
        $this->builder->setSender($wife);
        $this->builder->setRecipients(array($other));
        $thread = $this->builder->build();
        $this->assertInstanceOf('FOS\MessageBundle\Model\ThreadInterface', $thread);
        $this->assertFalse($thread->getMetadataForParticipant($other)->getIsDeleted());
        $this->assertFalse($thread->getMetadataForParticipant($wife)->getIsDeleted());
    }

    public function testBossCanStillAnnoyOthers()
    {
        $boss = new boss();
        $other = new OtherRecipient();
        $this->builder->setCreatedAt(new \DateTime('now'));
        $this->builder->setSubject('Write more code');
        $this->builder->setBody('And tests are for loosers!!!');
        $this->builder->setSender($boss);
        $this->builder->setRecipients(array($other));
        $thread = $this->builder->build();
        $this->assertInstanceOf('FOS\MessageBundle\Model\ThreadInterface', $thread);
        $this->assertFalse($thread->getMetadataForParticipant($other)->getIsDeleted());
    }
}

class boss implements ParticipantInterface
{
    public function getId()
    {
        return 'boss';
    }
}

class wife implements ParticipantInterface
{
    public function getId()
    {
        return 'wife';
    }
}

class OtherRecipient implements ParticipantInterface
{
    public function getId()
    {
        return 'poor_soul_who_has_nothing_to_do_here';
    }
}
