<?php

namespace Ornicar\MessageBundle\FormHandler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Ornicar\MessageBundle\Sender\SenderInterface;
use Ornicar\MessageBundle\FormModel\AbstractMessage;

abstract class AbstractMessageFormHandler
{
    protected $form;
    protected $request;
    protected $sender;

    public function __construct(Request $request, SenderInterface $sender)
    {
        $this->request = $request;
        $this->sender = $sender;
    }

    /**
     * Processes the form with the request
     *
     * @param Form $form
     * @return boolean true if the form is bound and valid, false otherwise
     */
    public function process(Form $form)
    {
        if ('POST' === $this->request->getMethod()) {

            $form->bindRequest($this->request);

            if ($form->isValid()) {
                $this->composeAndSend($form->getData());

                return true;
            }
        }

        return false;
    }

    /**
     * Sends the message
     *
     * @param AbstractMessage $message
     */
    abstract public function composeAndSend(AbstractMessage $message);
}
