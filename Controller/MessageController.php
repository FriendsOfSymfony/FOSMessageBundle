<?php

namespace Ornicar\MessageBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Ornicar\MessageBundle\Model\Message;
use Ornicar\MessageBundle\Model\Composition;
use Symfony\Component\DependencyInjection\ContainerAware;
use Ornicar\MessageBundle\Model\ThreadManagerInterface;
use Ornicar\MessageBundle\Model\ParticipantInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MessageController extends ContainerAware
{
    /**
     * Displays the authenticated user inbox
     *
     * @return Response
     */
    public function inboxAction()
    {
        $user = $this->getAuthenticatedParticipant();
        $threads = $this->getThreadManager()->findUserInboxThreads($user);

        return $this->container->get('templating')->renderResponse('OrnicarMessageBundle:Message:inbox.html.twig', array('threads' => $threads));
    }

    /**
     * Displays the authenticated user sent mails
     *
     * @return Response
     */
    public function sentAction()
    {
        $user = $this->getAuthenticatedParticipant();
        $threads = $this->getThreadManager()->findUserSentThreads($user);

        return $this->container->get('templating')->renderResponse('OrnicarMessageBundle:Message:sent.html.twig', array('threads' => $threads));
    }

    /**
     * Displays a thread, also allows to reply to it
     *
     * @param strind $threadId the thread id
     * @return Response
     */
    public function threadAction($threadId)
    {
        $thread = $this->container->get('ornicar_message.provider')->getThread($threadId);
        $form = $this->container->get('ornicar_message.reply_form.factory')->create($thread);
        $formHandler = $this->container->get('ornicar_message.reply_form.handler');

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('ornicar_message_thread_view', array(
                'threadId' => $message->getThread()->getId()
            )));
        }

        return $this->container->get('templating')->renderResponse('OrnicarMessageBundle:Message:thread.html.twig', array(
            'form' => $form->createView(),
            'thread' => $thread
        ));
    }

    /**
     * Create a new message thread
     *
     * @return Response
     */
    public function newThreadAction()
    {
        $form = $this->container->get('ornicar_message.new_thread_form.factory')->create();
        $formHandler = $this->container->get('ornicar_message.new_thread_form.handler');

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('ornicar_message_thread_view', array(
                'threadId' => $message->getThread()->getId()
            )));
        }

        return $this->container->get('templating')->renderResponse('OrnicarMessageBundle:Message:newThread.html.twig', array(
            'form' => $form->createView(),
            'data' => $form->getData()
        ));
    }

    /**
     * Deletes a thread
     *
     * @return Response
     */
    public function deleteAction($threadId)
    {
        $thread = $this->container->get('ornicar_message.provider')->getThread($threadId);
        $this->container->get('ornicar_message.thread_manager')->deleteThread($thread);

        return new RedirectResponse($this->container->get('router')->generate('ornicar_message_inbox'));
    }

    /**
     * Gets the current authenticated user
     *
     * @return ParticipantInterface
     */
    protected function getAuthenticatedParticipant()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!$user instanceof ParticipantInterface) {
            throw new AccessDeniedException('Must be logged in with a ParticipantInterface instance');
        }

        return $user;
    }

    /**
     * Gets the thread manager service
     *
     * @return ThreadManagerInterface
     */
    protected function getThreadManager()
    {
        return $this->container->get('ornicar_message.thread_manager');
    }
}
