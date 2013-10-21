<?php

namespace FOS\MessageBundle\FormHandler\Orm;

use FOS\MessageBundle\FormModel\ReplyMessage;
use Symfony\Component\HttpFoundation\Request;
use FOS\MessageBundle\Security\ParticipantProviderInterface;
use Symfony\Component\Form\Form;
use FOS\MessageBundle\MessageBuilder\Orm\ReplyBuilder;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\EntityManager\ActionsManagerInterface;

/**
 * Handles a reply from binding the request to passing it to the actionsmanager
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class ReplyThreadFormHandler extends AbstractFormHandler
{
    /**
     * A reply builder
     * 
     * @var ReplyBuilder
     */
    protected $replyBuilder;

    /**
     * An action manager instance
     *
     * @var ActionsManagerInterface
     */
    protected $actionsManager;

    /**
     * Constructor.
     *
     * @param ReplyBuilder                 $replyBuilder
     * @param ActionsManagerInterface      $actionsManager
     * @param Request                      $request
     * @param ParticipantProviderInterface $participantProvider
     */
    public function __construct(ReplyBuilder $replyBuilder, ActionsManagerInterface $actionsManager, Request $request, ParticipantProviderInterface $participantProvider)
    {
        $this->replyBuilder = $replyBuilder;
        $this->actionsManager = $actionsManager;
        parent::__construct($request, $participantProvider);
    }

    protected function updateThreadWithNewReply(ReplyMessage $message)
    {
        $this->replyBuilder->setThread($message->getThread());
        $this->replyBuilder->setBody($message->getBody());
        $this->replyBuilder->setSender($this->getAuthenticatedParticipant());
        $this->replyBuilder->setCreatedAt(new \DateTime('now'));

        //return the thread
        return $this->replyBuilder->build();
    }

    /**
     * {@inheritdoc}
     */
    public function createThreadObjectFromFormData(Form $form)
    {
        return $this->updateThreadWithNewReply($form->getData());
    }

     /**
     * {@inheritdoc}
     */
    public function persistThread(ThreadInterface $thread)
    {
       $this->actionsManager->addReply($thread);
    }
}
