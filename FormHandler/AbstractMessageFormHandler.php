<?php

namespace Ornicar\MessageBundle\FormHandler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Ornicar\MessageBundle\Composer\ComposerInterface;
use Ornicar\MessageBundle\FormModel\AbstractMessage;
use Ornicar\MessageBundle\Security\ParticipantProviderInterface;
use Ornicar\MessageBundle\Model\ParticipantInterface;
use Ornicar\MessageBundle\Sender\SenderInterface;

/**
 * Handles messages forms, from binding request to sending the message
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class AbstractMessageFormHandler
{
    protected $request;
    protected $composer;
    protected $sender;
    protected $participantProvider;

    public function __construct(Request $request, ComposerInterface $composer, SenderInterface $sender, ParticipantProviderInterface $participantProvider)
    {
        $this->request = $request;
        $this->composer = $composer;
        $this->sender = $sender;
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
            return $this->processValidForm($form);
        }

        return false;
    }

    /**
     * Processes the valid form, sends the message
     *
     * @param Form
     * @return MessageInterface the sent message
     */
    public function processValidForm(Form $form)
    {
		$message = $this->composeMessage($form->getData());

		$this->sender->send($message);

		return $message;
    }

    /**
     * Composes a message from the form data
     *
     * @param AbstractMessage $message
     * @return MessageInterface the composed message ready to be sent
     */
    abstract protected function composeMessage(AbstractMessage $message);

    /**
     * Gets the current authenticated user
     *
     * @return ParticipantInterface
     */
    protected function getAuthenticatedParticipant()
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }
}
