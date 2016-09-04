<?php

namespace FOS\MessageBundle\SpamDetection;

use FOS\MessageBundle\FormModel\NewThreadMessage;

class NoopSpamDetector implements SpamDetectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSpam(NewThreadMessage $message)
    {
        return false;
    }
}
