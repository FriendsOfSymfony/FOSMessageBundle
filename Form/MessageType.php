<?php

namespace Ornicar\MessageBundle\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

class MessageType extends AbstractType
{
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('to', 'text')
            ->add('subject', 'text')
            ->add('body', 'textarea');
    }
}