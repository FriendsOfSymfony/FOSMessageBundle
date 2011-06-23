<?php

namespace Ornicar\MessageBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Ornicar\MessageBundle\Model\Message;
use Ornicar\MessageBundle\Model\Composition;
use Symfony\Component\DependencyInjection\ContainerAware;
use Ornicar\MessageBundle\Model\ThreadManagerInterface;
use FOS\UserBundle\Model\UserInterface;
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
        $user = $this->getAuthenticatedUser();
        $threads = $this->getThreadManager()->findUserInboxThreads($user);

        return $this->container->get('templating')->renderResponse('OrnicarMessageBundle:Message:inbox.html.twig', array('threads' => $threads));
    }

    /**
     * Displays a thread
     *
     * @return Response
     */
    public function threadAction($threadId)
    {
        $thread = $this->container->get('ornicar_message.provider')->getThread($threadId);

        return $this->container->get('templating')->renderResponse('OrnicarMessageBundle:Message:thread.html.twig', array('thread' => $thread));
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
     * @return UserInterface
     */
    protected function getAuthenticatedUser()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException('Must be logged in with FOS\UserBundle');
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
