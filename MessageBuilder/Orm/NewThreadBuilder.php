<?php

namespace FOS\MessageBundle\MessageBuilder\Orm;

use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ThreadMetadata;

/**
 * The new thread builder is responsible for building a valid new thread object
 *
 * It is not able to return a valid object and throws an invalid argument error if
 * - Sender is not set
 * - Subject is not set
 * - Body is not set
 * - Creation date is not set
 *      - The form handlers set this to the current datetime but when programatically
 *        sending a message you have to set this in your code
 * - Sender is also a recipient (current code does not support this)
 * - No recipients set
 *
 * The builder also filters duplicate recipients but won't throw an error. (Code does not support this)
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class NewThreadBuilder extends AbstractMessageBuilder
{
    /**
     * The thread starter
     *
     * @var ParticipantInterface
     */
    protected $sender;

    /**
     * The recipients
     *
     * @var ParticipantInterface[] array of Recipients
     */
    protected $recipients = array();

    /**
     * Tells if a thread is marked as spam
     *
     * @var boolean defaults to false
     */
    protected $isSpam = false;

    /**
     * The subject of the thread
     *
     * @var string
     */
    protected $subject;

    /**
     * The thread that gets build
     *
     * @var ThreadInterface the thread we are building
     */
    protected $thread;

    /**
     * Sets the recipients of the thread
     *
     * We also validate here if it's really an array with participantinterfaces
     *
     * @param ParticipantInterface[] $recipients array of recipients
     *
     * @throws \InvalidArgumentException if not an array with participantinterfaces
     */
    public function setRecipients($recipients = array())
    {
        if (!is_array($recipients)) {
            throw new \InvalidArgumentException('SetRecipients requires an array as argument');
        }

        foreach ($recipients as $recipient) {
            if (!$recipient instanceof ParticipantInterface) {
                throw new \InvalidArgumentException('Recipients need to implement ParticipantInterface');
            }
        }

        $this->recipients = $recipients;
    }

    /**
     * Sets the subject of the thread
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Sets if the thread is marked as spam  or not
     *
     * @param boolean $boolean defaults to false
     *
     * @return NewThreadBuilder
     */
    public function setIsSpam($boolean)
    {
        $this->isSpam = $boolean;

        return $this;
    }

    /**
     * Builds the thread object
     *
     * @return ThreadInterface A new thread with all the parameters set
     */
    public function build()
    {
        $this->guardNewThreadRequirements();
        $this->filterDuplicateRecipients();
        //builds a new thread object with the thread data
        $thread = $this->buildNewThreadWithRequiredValues();

        $this->addParticipantsToThread($thread);

        //create the thread meta for the sender and add it
        $threadMetaSender = $this->buildThreadMetaForSender($thread);

        //create thread meta data for receivers
        foreach ($this->recipients as $recipient) {
            $this->buildThreadMetaForRecipient($thread, $recipient);
        }

        $this->buildNewMessage($thread);

        return $thread;
    }

    protected function guardNewThreadRequirements()
    {
        $this->expectsSenderSet();
        $this->expectsBodySet();
        $this->expectsCreationDate();
        $this->expectsSubjectSet();
        $this->expectsAtleastOneRecipient();
        $this->guardAgainstRecipientIsSender();
    }

    protected function expectsSubjectSet()
    {
        $subject = trim($this->subject);
        if (empty($subject)) {
            throw new \InvalidArgumentException('No subject set');
        }
    }

    protected function expectsAtleastOneRecipient()
    {
        if (empty($this->recipients)) {
            throw new \InvalidArgumentException('New thread requires at least one recipient');
        }
    }

    /**
     * The current code does not support that the sender is also a recipient
     * Because it relies on comparing the ids of the participants to get the meta datas
     * If we have two participants with the same id things will be broken
     */
    protected function guardAgainstRecipientIsSender()
    {
        $senderId = $this->sender->getId();

        $recipientIds = array();
        foreach ($this->recipients as $recipient) {
            $recipientIds[$recipient->getId()] = 1;
        }

       if (array_key_exists($senderId, $recipientIds)) {
            throw new \InvalidArgumentException('The sender can not be a receiver');
       }
    }

    //after doing unit tests it seems this is not really needed but i'm very hesistant to remove this
    protected function filterDuplicateRecipients()
    {
        if (count($this->recipients) === 1) {
            return;
        }
        //we could use array_unique sort_regular i guess but those objects can be very big
        //and we only use the getId in our interface
        $knownRecipients = array();
        foreach ($this->recipients as $recipient) {
            if (!array_key_exists($recipient->getId(), $knownRecipients)) {
                $filteredRecipients[] = $recipient;
                $knownRecipients[$recipient->getId()] = 1;
            }
        }
        $this->recipients = $filteredRecipients;
    }

    /**
     * Builds a new message
     *
     * @param ThreadInterface $thread
     *
     * @return MessageInterface
     */

    /**
     * Creates a new thread object
     *
     * @return ThreadInterface
     */
    protected function createThread()
    {
        return new $this->threadClass();
    }

    protected function createThreadMeta()
    {
        return new $this->threadMetaClass();
    }

    /**
     * Builds a new thread and sets the required values
     *
     * @return ThreadInterface
     */
    private function buildNewThreadWithRequiredValues()
    {
        $thread = $this->createThread();
        $thread->setCreatedAt($this->createdAt);
        $thread->setCreatedBy($this->sender);
        $thread->setSubject($this->subject);
        /**
         * @todo make this a requirement in the interface
         * it allready is since it's used in the orm and odm managers
         */
        $thread->setIsSpam(false);

        return $thread;
    }

    /**
     * Builds the thread meta for the sender
     *
     * @param ThreadInterface $thread
     */
    private function buildThreadMetaForSender(ThreadInterface $thread)
    {
        $threadMeta = $this->createThreadMetaForParticipant($thread, $this->sender);
        $this->updateThreadMetaForSender($threadMeta);
    }

    /**
     * Builds the thread meta for the recipient
     *
     * @param ThreadInterface      $thread
     * @param ParticipantInterface $recipient
     *
     * @return ThreadMetadata
     */
    private function buildThreadMetaForRecipient(ThreadInterface $thread, ParticipantInterface $recipient)
    {
        $threadMeta = $this->createThreadMetaForParticipant($thread, $recipient);
        $this->updateThreadMetaForRecipient($threadMeta);

        return $threadMeta;
    }

    /**
     * Adds participants to a thread
     *
     * @param ThreadInterface $thread
     *
     * @return ThreadInterface
     */
    private function addParticipantsToThread(ThreadInterface $thread)
    {
        $thread->addParticipant($this->sender);
        foreach ($this->recipients as $recipient) {
            $thread->addParticipant($recipient);
        }

        return $thread;
    }

    /**
     * Creates new threadmeta for the participant
     *
     * This creates new thread meta for the participant with the required settings set
     * It sets the
     * participant
     * current thread
     *
     * @param ThreadInterface      $thread
     * @param ParticipantInterface $participant
     *
     * @return ThreadMeta
     */
    private function createThreadMetaForParticipant(ThreadInterface $thread, ParticipantInterface $participant)
    {
        //creation of the thread meta
        $threadMeta = $this->createThreadMeta();
        $threadMeta->setParticipant($participant);
        $threadMeta->setThread($thread);
        $thread->addMetadata($threadMeta);

        return $threadMeta;
    }
}
