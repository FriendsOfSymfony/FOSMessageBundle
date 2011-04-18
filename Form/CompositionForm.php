<?php

namespace Ornicar\MessageBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\TextareaField;

class CompositionForm extends Form
{
    public function configure()
    {
        $this->add(new TextField('to'));
        $this->add(new TextField('subject'));
        $this->add(new TextareaField('body'));
    }
}
