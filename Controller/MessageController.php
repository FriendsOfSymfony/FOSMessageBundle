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
// use Symfony\Component\DependencyInjection\ContainerInterface;

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

    // public function __construct(ContainerInterface $container)
    // {
    //     $this->setContainer($container);
    // }
    public function __construct(
        ProviderInterface $provider,
        FactoryInterface $replyFormfactory,
        FormHandlerInterface $replyFormHandler,
        FactoryInterface $newThreadFormFactory,
        FormHandlerInterface $newThreadFormHandler
    ) {
        $this->provider = $provider;
        $this->replyFormfactory = $replyFormfactory;
        $this->replyFormHandler = $replyFormHandler;
        $this->newThreadFormFactory = $newThreadFormFactory;
        $this->newThreadFormHandler = $newThreadFormHandler;
    }

    /**
     * Displays the authenticated participant inbox.
     *
     * @return Response
     */
    public function inboxAction()
    {
        $threads = $this->provider->getInboxThreads();

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
        $threads = $this->provider->getSentThreads();

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
        $threads = $this->provider->getDeletedThreads();

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
        $thread = $this->provider->getThread($threadId);
        // $form = $this->container->get('fos_message.reply_form.factory')->create($thread);
        $form = $this->replyFormfactory->create($thread);
        // $formHandler = $this->container->get('fos_message.reply_form.handler');

        if ($message = $this->replyFormHandler->process($form)) {
            return $this->redirectToRoute('fos_message_thread_view', ['threadId' => $message->getThread()->getId()]);
            // return new RedirectResponse($this->container->get('router')->generate('fos_message_thread_view', array(
            //     'threadId' => $message->getThread()->getId(),
            // )));
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
        // $form = $this->container->get('fos_message.new_thread_form.factory')->create();
        $form = $this->newThreadFormFactory->create();
        // $formHandler = $this->container->get('fos_message.new_thread_form.handler');

        if ($message = $this->newThreadFormHandler->process($form)) {
            return $this->redirectToRoute('fos_message_thread_view', ['threadId' => $message->getThread()->getId()]);
            // return new RedirectResponse($this->container->get('router')->generate('fos_message_thread_view', array(
            //     'threadId' => $message->getThread()->getId(),
            // )));
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
    public function deleteAction($threadId, DeleterInterface $deleter, ThreadManagerInterface $threadManager)
    {
        $thread = $this->provider->getThread($threadId);
        // $this->container->get('fos_message.deleter')->markAsDeleted($thread);
        $deleter->markAsDeleted($thread);
        // $this->container->get('fos_message.thread_manager')->saveThread($thread);
        $threadManager->saveThread($thread);

        // return new RedirectResponse($this->container->get('router')->generate('fos_message_inbox'));
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
        // $this->container->get('fos_message.deleter')->markAsUndeleted($thread);
        // $this->container->get('fos_message.thread_manager')->saveThread($thread);
        $deleter->markAsUndeleted($thread);
        $threadManager->saveThread($thread);

        // return new RedirectResponse($this->container->get('router')->generate('fos_message_inbox'));
        return $this->redirectToRoute('fos_message_inbox');
    }

    /**
     * Searches for messages in the inbox and sentbox.
     *
     * @return Response
     */
    public function searchAction(QueryFactoryInterface $queryFactory, FinderInterface $finder)
    {
        // $query = $this->container->get('fos_message.search_query_factory')->createFromRequest();
        // $threads = $this->container->get('fos_message.search_finder')->find($query);
        $query = $queryFactory->createFromRequest();
        $threads = $finder->find($query);

        return $this->render('@FOSMessage/Message/search.html.twig', array(
            'query' => $query,
            'threads' => $threads,
        ));
    }
}
