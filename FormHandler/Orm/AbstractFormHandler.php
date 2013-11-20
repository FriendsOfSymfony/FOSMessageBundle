<?php

namespace FOS\MessageBundle\FormHandler\Orm;

use Symfony\Component\HttpFoundation\Request;
use FOS\MessageBundle\Security\ParticipantProviderInterface;
use Symfony\Component\Form\Form;
use FOS\MessageBundle\Model\ThreadInterface;

/**
 * Handles messages forms, from binding request to adding it to the storage
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
abstract class AbstractFormHandler
{
    /**
     * The request the form will process
     *
     * @var Request
     */
    protected $request;

    /**
     * A participant provider instance
     *
     * @var ParticipantProvider
     */
    protected $participantProvider;

    /**
     * Constructor.
     *
     * @param Request             $request             The request the form will process
     * @param ParticipantProvider $participantProvider A participantprovider instance
     */
    public function __construct(Request $request, ParticipantProviderInterface $participantProvider)
    {
        $this->request = $request;
        $this->participantProvider = $participantProvider;
    }

    /**
     * Processes the form with the request
     *
     * @param Form $form A form instance
     *
     * @return Message|false the last message if the form is valid, false otherwise
     */
    public function process(Form $form)
    {
        if ('POST' !== $this->request->getMethod()) {
            return false;
        }

        $form->bind($this->request);

        if ($form->isValid()) {
            $thread = $this->createThreadObjectFromFormData($form);
            $this->persistThread($thread);

            return $thread->getLastMessage();
        }

        return false;
    }

    /**
     * Gets the new thread object with the form data processed
     *
     * @param Form $form The valid form
     *
     * @return ThreadInterface The thread object after it is processed with the form data
     */
    abstract public function createThreadObjectFromFormData(Form $form);

    /**
     * Saves the thread to the storage engine
     *
     * @param ThreadInterface $thread
     */
    abstract public function persistThread(ThreadInterface $thread);

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
