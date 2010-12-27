<?php

namespace Bundle\Ornicar\MessageBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\HiddenField;
use Symfony\Component\Form\TextareaField;

class AnswerForm extends Form
{
    public function configure()
    {
        $this->add(new HiddenField('to'));
        $this->add(new HiddenField('subject'));
        $this->add(new TextareaField('body'));
    }
}
