<?php

namespace FOS\MessageBundle\Form\Factory;

use FOS\MessageBundle\Model\ThreadInterface;

/**
 * Instanciates message forms
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ReplyMessageFormFactory extends AbstractMessageFormFactory
{
    /**
     * Creates a reply message
     *
     * @param  ThreadInterface $thread the thread we answer to
     * @return \Symfony\Component\Form\FormInterface
     */
    public function create(ThreadInterface $thread)
    {
        $message = $this->createModelInstance();
        $message->setThread($thread);

        return $this->formFactory->createNamed($this->formName, $this->formType, $message);
    }
}
