<?php

namespace FOS\MessageBundle\Controller;

use FOS\MessageBundle\Provider\ProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

//Dependency Injection
use Symfony\Component\Routing\RouterInterface;

use FOS\MessageBundle\FormFactory\ReplyMessageFormFactory;
use FOS\MessageBundle\FormHandler\ReplyMessageFormHandler;

use FOS\MessageBundle\FormFactory\NewThreadMessageFormFactory;
use FOS\MessageBundle\FormHandler\NewThreadMessageFormHandler;

use FOS\MessageBundle\Deleter\Deleter;
use FOS\MessageBundle\EntityManager\ThreadManager;

use FOS\MessageBundle\Search\QueryFactory;
use FOS\MessageBundle\Search\Finder;

use FOS\MessageBundle\Provider\Provider;


// service names

// fos_message.reply_form.factory
// fos_message.reply_form.handler

// router

// fos_message.new_thread_form.factory
// fos_message.new_thread_form.handler

// fos_message.deleter
// fos_message.thread_manager

// fos_message.search_query_factory
// fos_message.search_finder

// fos_message.provider

class MessageController extends AbstractController
{
    protected $router;
    protected $replyFormFactory;
    protected $replyFormHandler;
    protected $newThreadFormFactory;
    protected $newThreadFormHandler;
    protected $deleter;
    protected $threadManager;
    protected $searchQueryFactory;
    protected $searchFinder;
    protected $provider;

    public function __construct(
        $router,
        $replyFormFactory,
        $replyFormHandler,
        $newThreadFormFactory,
        $newThreadFormHandler,
        $deleter,
        $threadManager,
        $searchQueryFactory,
        $searchFinder,
        $provider
    )
    {
        $this->router = $router;
        $this->replyFormFactory = $replyFormFactory;
        $this->replyFormHandler = $replyFormHandler;
        $this->newThreadFormFactory = $newThreadFormFactory;
        $this->newThreadFormHandler = $newThreadFormHandler;
        $this->deleter = $deleter;
        $this->threadManager = $threadManager;
        $this->searchQueryFactory = $searchQueryFactory;
        $this->searchFinder = $searchFinder;
        $this->provider = $provider;
    }

    /**
     * Displays the authenticated participant inbox.
     *
     * @return Response
     */
    public function inboxAction()
    {
        $threads = $this->getProvider()->getInboxThreads();

        return $this->render('@FOSMessage/Message/inbox.html.twig', array(
            'threads' => $threads,
        ));
    }

    /**
     * Displays the authenticated participant messages sent.
     *
     * @return Response
     */
    public function sentAction()
    {
        $threads = $this->getProvider()->getSentThreads();

        return $this->render('@FOSMessage/Message/sent.html.twig', array(
            'threads' => $threads,
        ));
    }

    /**
     * Displays the authenticated participant deleted threads.
     *
     * @return Response
     */
    public function deletedAction()
    {
        $threads = $this->getProvider()->getDeletedThreads();

        return $this->render('@FOSMessage/Message/deleted.html.twig', array(
            'threads' => $threads,
        ));
    }

    /**
     * Displays a thread, also allows to reply to it.
     *
     * @param string $threadId the thread id
     *
     * @return Response
     */
    public function threadAction($threadId)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $form = $this->replyFormFactory->create($thread);
        $formHandler = $this->replyFormHandler;

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->router->generate('fos_message_thread_view', array(
                'threadId' => $message->getThread()->getId(),
            )));
        }

        return $this->render('@FOSMessage/Message/thread.html.twig', array(
            'form' => $form->createView(),
            'thread' => $thread,
        ));
    }

    /**
     * Create a new message thread.
     *
     * @return Response
     */
    public function newThreadAction()
    {
        $form = $this->newThreadFormFactory->create();
        $formHandler = $this->newThreadFormHandler->get('fos_message.new_thread_form.handler');

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->router->generate('fos_message_thread_view', array(
                'threadId' => $message->getThread()->getId(),
            )));
        }

        return $this->render('@FOSMessage/Message/newThread.html.twig', array(
            'form' => $form->createView(),
            'data' => $form->getData(),
        ));
    }

    /**
     * Deletes a thread.
     *
     * @param string $threadId the thread id
     *
     * @return RedirectResponse
     */
    public function deleteAction($threadId)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $this->deleter->markAsDeleted($thread);
        $this->threadManager->saveThread($thread);

        return new RedirectResponse($this->router->generate('fos_message_inbox'));
    }

    /**
     * Undeletes a thread.
     *
     * @param string $threadId
     *
     * @return RedirectResponse
     */
    public function undeleteAction($threadId)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $this->deleter->markAsUndeleted($thread);
        $this->threadManager->saveThread($thread);

        return new RedirectResponse($this->router->generate('fos_message_inbox'));
    }

    /**
     * Searches for messages in the inbox and sentbox.
     *
     * @return Response
     */
    public function searchAction()
    {
        $query = $this->searchQueryFactory->createFromRequest();
        $threads = $this->searchFinder->find($query);

        return $this->render('@FOSMessage/Message/search.html.twig', array(
            'query' => $query,
            'threads' => $threads,
        ));
    }

    /**
     * Gets the provider service.
     *
     * @return ProviderInterface
     */
    protected function getProvider()
    {
        return $this->provider;
    }
}
