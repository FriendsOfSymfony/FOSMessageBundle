<?php

namespace Ornicar\MessageBundle\FormHandler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Ornicar\MessageBundle\Composer\ComposerInterface;
use Ornicar\MessageBundle\FormModel\AbstractMessage;
use Ornicar\MessageBundle\Security\ParticipantProviderInterface;
use Ornicar\MessageBundle\Model\ParticipantInterface;

abstract class AbstractMessageFormHandler
{
    protected $form;
    protected $request;
    protected $composer;
    protected $participantProvider;

    public function __construct(Request $request, ComposerInterface $composer, ParticipantProviderInterface $participantProvider)
    {
        $this->request = $request;
        $this->composer = $composer;
        $this->participantProvider = $participantProvider;
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
     * @return ParticipantInterface
     */
    public function getAuthenticatedParticipant()
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }
}
