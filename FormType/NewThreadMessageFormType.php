<?php

namespace Ornicar\MessageBundle\FormType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Message form type for starting a new conversation
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NewThreadMessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('collection', array(
                'type' => 'fos_user_username', 
                'allow_add' => true, 
                'by_reference' => false
            ))
            ->add('subject', 'text')
            ->add('body', 'textarea');
    }

    public function getName()
    {
        return 'ornicar_message_new_thread';
    }
}
