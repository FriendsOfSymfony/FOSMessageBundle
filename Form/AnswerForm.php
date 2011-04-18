<?php

namespace Ornicar\MessageBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\HiddenField;
use Symfony\Component\Form\TextareaField;
use Ornicar\MessageBundle\Model\Message;

class AnswerForm extends Form
{
    public function configure()
    {
        $this->add(new HiddenField('to'));
        $this->add(new HiddenField('subject'));
        $this->add(new TextareaField('body'));
    }

    public function setOriginalMessage(Message $message)
    {
        $answer = $this->getData();
        $answer->setOriginalMessage($message);
        $this['to']->setData($answer->to);
        $this['subject']->setData($answer->subject);
    }
}
