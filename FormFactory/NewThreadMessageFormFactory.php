<?php

namespace FOS\MessageBundle\FormFactory;

use FOS\MessageBundle\Model\ThreadInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Instanciates message forms.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NewThreadMessageFormFactory extends AbstractMessageFormFactory
{
    /**
     * Creates a new thread message.
     *
     * @return FormInterface
     */
    public function create(?ThreadInterface $thread = null)
    {
        return $this->formFactory->createNamed($this->formName, $this->formType, $this->createModelInstance());
    }
}
