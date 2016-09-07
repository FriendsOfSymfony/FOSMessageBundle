<?php

namespace FOS\MessageBundle\FormFactory;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;
use FOS\MessageBundle\FormModel\AbstractMessage;

/**
 * Instanciates message forms.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class AbstractMessageFormFactory
{
    /**
     * The Symfony form factory.
     *
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * The message form type.
     *
     * @var AbstractType|string
     */
    protected $formType;

    /**
     * The name of the form.
     *
     * @var string
     */
    protected $formName;

    /**
     * The FQCN of the message model.
     *
     * @var string
     */
    protected $messageClass;

    public function __construct(FormFactoryInterface $formFactory, $formType, $formName, $messageClass)
    {
        if (!is_string($formType) && !$formType instanceof AbstractType) {
            throw new \InvalidArgumentException(sprintf(
                'Form type provided is not valid (class name or instance of %s expected, %s given)',
                'Symfony\Component\Form\AbstractType',
                is_object($formType) ? get_class($formType) : gettype($formType)
            ));
        }

        $this->formFactory = $formFactory;
        $this->formType = $formType;
        $this->formName = $formName;
        $this->messageClass = $messageClass;
    }

    /**
     * Creates a new instance of the form model.
     *
     * @return AbstractMessage
     */
    protected function createModelInstance()
    {
        $class = $this->messageClass;

        return new $class();
    }
}
