<?php

namespace FOS\MessageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Message form type for starting a new conversation with multiple recipients
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class NewThreadMultipleMessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('recipients', 'recipients_selector')
            ->add('subject', 'text')
            ->add('body', 'textarea');
    }

    public function getName()
    {
        return 'fos_message_new_multiperson_thread';
    }
}
