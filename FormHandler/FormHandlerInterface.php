<?php

namespace FOS\MessageBundle\FormHandler;

use FOS\MessageBundle\Model\MessageInterface;
use Symfony\Component\Form\Form;

interface FormHandlerInterface
{
    /**
     * Processes the form with the request.
     *
     * @param Form $form
     *
     * @return MessageInterface|false the sent message if the form is bound and valid, false otherwise
     */
    public function process(Form $form);

    /**
     * Processes the valid form, sends the message.
     *
     * @param Form $form
     *
     * @return MessageInterface the sent message
     */
    public function processValidForm(Form $form);
}