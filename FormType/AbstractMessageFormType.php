<?php

namespace Ornicar\MessageBundle\FormType;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

/**
 * Abstract message form type used for both reply and new thread
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class AbstractMessageFormType extends AbstractType
{
    /**
     * The FQCN of the message model
     *
     * @var string
     */
    protected $messageClass;

    public function __construct($messageClass)
    {
        $this->messageClass = $messageClass;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('body', 'textarea');
    }
}
