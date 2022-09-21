<?php

namespace FOS\MessageBundle\FormFactory;

use FOS\MessageBundle\Model\ThreadInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Instanciates message forms.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ReplyMessageFormFactory extends AbstractMessageFormFactory
{
    /**
     * Creates a reply message.
     *
     * @param ThreadInterface $thread the thread we answer to
     *
     * @return FormInterface
     */
    public function create(?ThreadInterface $thread = null)
    {
        $message = $this->createModelInstance();
        $message->setThread($thread);

        return $this->formFactory->createNamed($this->formName, $this->formType, $message);
    }
}
