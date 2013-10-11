<?php

namespace FOS\MessageBundle\EntityManager;

use Doctrine\ORM\EntityManager;
use FOS\MessageBundle\Entity\Message;

/**
 * Handles de-normalizing the ORM message objects.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class MessageDenormalizer
{
    /**
     * The metadata model class
     *
     * @var string
     */
    protected $metaClass;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     * @param string $metaClass
     */
    public function __construct(EntityManager $em, $metaClass)
    {
        $this->metaClass = $em->getClassMetadata($metaClass)->name;
    }

    /**
     * Performs denormalization on a Doctrine ORM Message entity.
     *
     * @param Message $message
     */
    public function denormalize(Message $message)
    {
        $this->doMetadata($message);
    }

    /**
     * Ensures that the message metadata is up to date
     *
     * @param Message $message
     */
    protected function doMetadata(Message $message)
    {
        foreach ($message->getThread()->getAllMetadata() as $threadMeta) {
            /** @var \FOS\MessageBundle\Model\ThreadMetadata $threadMeta */
            $meta = $message->getMetadataForParticipant($threadMeta->getParticipant());
            if (!$meta) {
                $meta = $this->createMessageMetadata();
                $meta->setParticipant($threadMeta->getParticipant());
                $message->addMetadata($meta);
            }
        }
    }

    /**
     * @return \FOS\MessageBundle\Model\MessageMetadata
     */
    protected function createMessageMetadata()
    {
        return new $this->metaClass();
    }
}
