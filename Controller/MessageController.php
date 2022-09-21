<?php

namespace FOS\MessageBundle\Controller;

use FOS\MessageBundle\Deleter\DeleterInterface;
use FOS\MessageBundle\FormFactory\FactoryInterface;
use FOS\MessageBundle\FormHandler\FormHandlerInterface;
use FOS\MessageBundle\ModelManager\ThreadManagerInterface;
use FOS\MessageBundle\Provider\ProviderInterface;
use FOS\MessageBundle\Search\FinderInterface;
use FOS\MessageBundle\Search\QueryFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends AbstractController
{
    /**
     * @var ProviderInterface
     */
    protected $provider;

    /**
     * @var FactoryInterface
     */
    protected $replyFormfactory;

    /**
     * @var FormHandlerInterface
     */
    protected $replyFormHandler;

    /**
     * @var FactoryInterface
     */
    protected $newThreadFormFactory;

    /**
     * @var FormHandlerInterface
     */
    protected $newThreadFormHandler;

    /**
     * @var DeleterInterface
     */
    protected $deleter;

    /**
     * @var ThreadManagerInterface
     */
    protected $threadManager;

    /**
     * @var QueryFactoryInterface
     */
    protected $queryFactory;

    /**
     * @var FinderInterface
     */
    protected $finder;

    public function __construct(
        ProviderInterface $provider,
        FactoryInterface $replyFormfactory,
        FormHandlerInterface $replyFormHandler,
        FactoryInterface $newThreadFormFactory,
        FormHandlerInterface $newThreadFormHandler,
        DeleterInterface $deleter,
        ThreadManagerInterface $threadManager,
        QueryFactoryInterface $queryFactory,
        FinderInterface $finder
    ) {
        $this->provider = $provider;
        $this->replyFormfactory = $replyFormfactory;
        $this->replyFormHandler = $replyFormHandler;
        $this->newThreadFormFactory = $newThreadFormFactory;
        $this->newThreadFormHandler = $newThreadFormHandler;
        $this->deleter = $deleter;
        $this->threadManager = $threadManager;
        $this->queryFactory = $queryFactory;
        $this->finder = $finder;
    }

    /**
     * Displays the authenticated participant inbox.
     *
     * @return Response
     */
    public function inboxAction()
    {
        $threads = $this->provider->getInboxThreads();

        return $this->render('@FOSMessage/Message/inbox.html.twig', ['threads' => $threads]);
    }

    /**
     * Displays the authenticated participant messages sent.
     *
     * @return Response
     */
    public function sentAction()
    {
        $threads = $this->provider->getSentThreads();

        return $this->render('@FOSMessage/Message/sent.html.twig', ['threads' => $threads]);
    }

    /**
     * Displays the authenticated participant deleted threads.
     *
     * @return Response
     */
    public function deletedAction()
    {
        $threads = $this->provider->getDeletedThreads();

        return $this->render('@FOSMessage/Message/deleted.html.twig', ['threads' => $threads]);
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
        $thread = $this->provider->getThread($threadId);
        $form = $this->replyFormfactory->create($thread);

        if ($message = $this->replyFormHandler->process($form)) {
            return $this->redirectToRoute('fos_message_thread_view', ['threadId' => $message->getThread()->getId()]);
        }

        return $this->render('@FOSMessage/Message/thread.html.twig', [
            'form' => $form->createView(),
            'thread' => $thread
        ]);
    }

    /**
     * Create a new message thread.
     *
     * @return Response
     */
    public function newThreadAction()
    {
        $form = $this->newThreadFormFactory->create();

        if ($message = $this->newThreadFormHandler->process($form)) {
            return $this->redirectToRoute('fos_message_thread_view', ['threadId' => $message->getThread()->getId()]);
        }

        return $this->render('@FOSMessage/Message/newThread.html.twig', [
            'form' => $form->createView(),
            'data' => $form->getData()
        ]);
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
        $thread = $this->provider->getThread($threadId);
        $this->deleter->markAsDeleted($thread);
        $this->threadManager->saveThread($thread);

        return $this->redirectToRoute('fos_message_inbox');
    }

    /**
     * Undeletes a thread.
     *
     * @param string $threadId
     *
     * @return RedirectResponse
     */
    public function undeleteAction($threadId, DeleterInterface $deleter, ThreadManagerInterface $threadManager)
    {
        $thread = $this->provider->getThread($threadId);
        $deleter->markAsUndeleted($thread);
        $threadManager->saveThread($thread);

        return $this->redirectToRoute('fos_message_inbox');
    }

    /**
     * Searches for messages in the inbox and sentbox.
     *
     * @return Response
     */
    public function searchAction()
    {
        $query = $this->queryFactory->createFromRequest();
        $threads = $this->finder->find($query);

        return $this->render('@FOSMessage/Message/search.html.twig', ['query' => $query, 'threads' => $threads]);
    }
}
