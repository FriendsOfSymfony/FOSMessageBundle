<?php

namespace Ornicar\MessageBundle\FormFactory;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;

class MessageFormFactory
{
    /**
     * The Symfony form factory
     *
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * The message form type
     *
     * @var AbstractType
     */
    protected $formType;

    /**
     * The name of the form
     *
     * @var string
     */
    protected $formName;

    public function __construct(FormFactoryInterface $formFactory, AbstractType $formType, $formName)
    {
        $this->formFactory = $formFactory;
        $this->formType = $formType;
        $this->formName = $formName;
    }

    public function create()
    {
        return $this->formFactory->createNamed($this->formType, $this->formName);
    }
}
