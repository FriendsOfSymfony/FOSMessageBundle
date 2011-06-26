<?php

namespace Ornicar\MessageBundle\FormHandler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Ornicar\MessageBundle\Composer\ComposerInterface;
use Ornicar\MessageBundle\FormModel\AbstractMessage;
use Ornicar\MessageBundle\Authorizer\AuthorizerInterface;
use FOS\UserBundle\Model\UserInterface;

abstract class AbstractMessageFormHandler
{
    protected $form;
    protected $request;
    protected $composer;
    protected $authorizer;

    public function __construct(Request $request, ComposerInterface $composer, AuthorizerInterface $authorizer)
    {
        $this->request = $request;
        $this->composer = $composer;
        $this->authorizer = $authorizer;
    }

    /**
     * Processes the form with the request
     *
     * @param Form $form
     * @return Message|false the sent message if the form is bound and valid, false otherwise
     */
    public function process(Form $form)
    {
        if ('POST' !== $this->request->getMethod()) {
            return false;
        }

        $form->bindRequest($this->request);

        if ($form->isValid()) {
            return $this->composeAndSend($form->getData());
        }

        return false;
    }

    /**
     * Sends the message
     *
     * @param AbstractMessage $message
     */
    abstract protected function composeAndSend(AbstractMessage $message);

    /**
     * Gets the current authenticated user
     *
     * @return UserInterface
     */
    public function getAuthenticatedUser()
    {
        return $this->authorizer->getAuthenticatedUser();
    }
}
