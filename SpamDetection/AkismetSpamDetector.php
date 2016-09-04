<?php

namespace FOS\MessageBundle\SpamDetection;

use Ornicar\AkismetBundle\Akismet\AkismetInterface;
use FOS\MessageBundle\FormModel\NewThreadMessage;
use FOS\MessageBundle\Security\ParticipantProviderInterface;

class AkismetSpamDetector implements SpamDetectorInterface
{
    /**
     * @var AkismetInterface
     */
    protected $akismet;

    /**
     * @var ParticipantProviderInterface
     */
    protected $participantProvider;

    public function __construct(AkismetInterface $akismet, ParticipantProviderInterface $participantProvider)
    {
        $this->akismet = $akismet;
        $this->participantProvider = $participantProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function isSpam(NewThreadMessage $message)
    {
        return $this->akismet->isSpam(array(
            'comment_author' => (string) $this->participantProvider->getAuthenticatedParticipant(),
            'comment_content' => $message->getBody(),
        ));
    }
}
