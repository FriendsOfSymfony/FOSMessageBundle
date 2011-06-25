<?php

namespace Ornicar\MessageBundle\FormType;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

/**
 * Message form type for starting a new conversation
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NewThreadMessageFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('recipient', 'ornicar_message.username')
            ->add('subject', 'text')
            ->add('body', 'textarea');
    }
}
