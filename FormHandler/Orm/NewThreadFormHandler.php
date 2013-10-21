<?php

namespace FOS\MessageBundle\FormHandler\Orm;

use FOS\MessageBundle\MessageBuilder\Orm\NewThreadBuilder;
use FOS\MessageBundle\FormHandler\Orm\AbstractFormHandler;
use Symfony\Component\HttpFoundation\Request;
use FOS\MessageBundle\Security\ParticipantProviderInterface;
use Symfony\Component\Form\Form;
use FOS\MessageBundle\EntityManager\ActionsManagerInterface;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\FormModel\NewThreadMessage;

/**
 * Handles a new thread from binding the request to passing it to the actions manager
 * 
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class NewThreadFormHandler extends AbstractFormHandler
{
    /**
     * A new thread builder instance
     *
     * @var NewThreadBuilder
     */
    protected $newThreadBuilder;

    /**
     * An action manager instance
     * 
     * @var ActionsManagerInterface
     */
    protected $actionsManager;

    /**
     * Constructor.
     *
     * @param NewThreadBuilder             $newThreadBuilder    A new thread builder instance
     * @param ActionsManagerInterface      $actionsManager      An actions manager instance
     * @param Request                      $request             The request for the form
     * @param ParticipantProviderInterface $participantProvider A participant provider instance
     */
    public function __construct(NewThreadBuilder $newThreadBuilder, ActionsManagerInterface $actionsManager, Request $request, ParticipantProviderInterface $participantProvider)
    {
        $this->newThreadBuilder = $newThreadBuilder;
        $this->actionsManager = $actionsManager;
        parent::__construct($request, $participantProvider);
    }

    /**
     * {@inheritdoc}
     */
    public function createThreadObjectFromFormData(Form $form)
    {
        return $this->createNewThreadFromFormData($form->getData());
    }

    /**
     * {@inheritdoc}
     */
    public function persistThread(ThreadInterface $thread)
    {
        $this->actionsManager->addThread($thread);
    }

    /**
     * Creates a new thread from the form data
     *
     * @param NewThreadMessage $message A new thread message
     *
     * @return ThreadInterface the new build thread
     */
    protected function createNewThreadFromFormData(NewThreadMessage $message)
    {
        $this->newThreadBuilder->setCreatedAt(new \DateTime('now'));
        $this->newThreadBuilder->setSubject($message->getSubject());
        $this->newThreadBuilder->setBody($message->getBody());
        $this->newThreadBuilder->setRecipients(array($message->getRecipient()));
        $this->newThreadBuilder->setIsSpam(false);
        $this->newThreadBuilder->setSender($this->getAuthenticatedParticipant());

        return $this->newThreadBuilder->build();
    }
}
