<?php

namespace Ornicar\MessageBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Ornicar\MessageBundle\Model\Message;
use Ornicar\MessageBundle\Model\Composition;
use Symfony\Component\DependencyInjection\ContainerAware;
use Ornicar\MessageBundle\Model\ThreadManagerInterface;
use Ornicar\MessageBundle\Model\ParticipantInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MessageController extends ContainerAware
{
    /**
     * Displays the authenticated participant inbox
     *
     * @return Response
     */
    public function inboxAction()
    {
        $participant = $this->getAuthenticatedParticipant();
        $threads = $this->getThreadManager()->findParticipantInboxThreads($participant);

        return $this->container->get('templating')->renderResponse('OrnicarMessageBundle:Message:inbox.html.twig', array('threads' => $threads));
    }

    /**
     * Displays the authenticated participant sent mails
     *
     * @return Response
     */
    public function sentAction()
    {
        $participant = $this->getAuthenticatedParticipant();
        $threads = $this->getThreadManager()->findParticipantSentThreads($participant);

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
        $this->container->get('ornicar_message.deleter')->delete($thread);

        return new RedirectResponse($this->container->get('router')->generate('ornicar_message_inbox'));
    }

    /**
     * Searches for messages in the inbox and sentbox
     *
     * @return Response
     */
    public function searchAction()
    {
        $query = $this->container->get('ornicar_message.search_query_factory')->createFromRequest();
        $threads = $this->container->get('ornicar_message.search_finder')->find($query);

        return $this->container->get('templating')->renderResponse('OrnicarMessageBundle:Message:search.html.twig', array(
            'query' => $query,
            'threads' => $threads
        ));
    }

    /**
     * Gets the current authenticated participant
     *
     * @return ParticipantInterface
     */
    protected function getAuthenticatedParticipant()
    {
        return $this->container->get('ornicar_message.authorizer')->getAuthenticatedParticipant();
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
