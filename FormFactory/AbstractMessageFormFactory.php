<?php

namespace FOS\MessageBundle\FormFactory;

use Symfony\Component\Form\FormFactoryInterface;
use FOS\MessageBundle\FormModel\AbstractMessage;

/**
 * Instanciates message forms
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class AbstractMessageFormFactory
{
    /**
     * The Symfony form factory
     *
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * The message form class
     *
     * @var string
     */
    protected $formClass;

    /**
     * The name of the form
     *
     * @var string
     */
    protected $formName;

    /**
     * The FQCN of the message model
     *
     * @var string
     */
    protected $messageClass;

    public function __construct(FormFactoryInterface $formFactory, $formClass, $formName, $messageClass)
    {
        $this->formFactory = $formFactory;
        $this->formClass = $formClass;
        $this->formName = $formName;
        $this->messageClass = $messageClass;
    }

    /**
     * Creates a new instance of the form model
     *
     * @return AbstractMessage
     */
    protected function createModelInstance()
    {
        $class = $this->messageClass;

        return new $class();
    }
}
