<?php

namespace Bundle\Ornicar\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Bundle\Ornicar\MessageBundle\Model\Message;

class MessageController extends Controller
{
    public function newAction()
    {
        $form = $this->get('ornicar_message.form.composition');
        $form['to']->setData($this->get('request')->query->get('to'));

        return $this->render('MessageBundle:Message:new.html.twig', array(
            'form' => $form
        ));
    }

    public function createAction()
    {
        $form = $this->get('ornicar_message.form.composition');
        $form->bind($this->get('request')->request->get($form->getName()));

        if ($form->isValid()) {
            $message = $form->getData()->getMessage();
            $message->setFrom($this->get('security.context')->getUser());
            $this->get('ornicar_message.messenger')->send($message);
            $this->get('ornicar_message.object_manager')->flush();
            $this->get('session')->setFlash('ornicar_message_message_create', 'success');

            return $this->redirect($this->generateUrl('ornicar_message_message_sent'));
        }

        return $this->render('MessageBundle:Message:new.html.twig', array(
            'form' => $form
        ));
    }

    public function listAction()
    {
        $user = $this->get('security.context')->getUser();
        $messages = $this->get('ornicar_message.repository.message')->findRecentByUser($user, true);
        $messages->setCurrentPageNumber($this->get('request')->query->get('page', 1));
        $messages->setItemCountPerPage($this->container->getParameter('ornicar_message.paginator.messages_per_page'));
        $messages->setPageRange(5);

        return $this->render('MessageBundle:Message:list.html.twig', array(
            'messages' => $messages
        ));
    }

    public function sentAction()
    {
        $user = $this->get('security.context')->getUser();
        $messages = $this->get('ornicar_message.repository.message')->findRecentSentByUser($user, true);
        $messages->setCurrentPageNumber($this->get('request')->query->get('page', 1));
        $messages->setItemCountPerPage($this->container->getParameter('ornicar_message.paginator.messages_per_page'));
        $messages->setPageRange(5);

        return $this->render('MessageBundle:Message:sent.html.twig', array(
            'messages' => $messages
        ));
    }

    public function showAction($id)
    {
        $message = $this->getVisibleMessage($id);
        $this->markAsRead($message);
        if($message->getTo()->is($this->get('security.context')->getUser())) {
            $form = $this->get('ornicar_message.form.answer');
            $form->setOriginalMessage($message);
        } else {
            $form = null;
        }

        return $this->render('MessageBundle:Message:show.html.twig', array(
            'message' => $message,
            'form' => $form
        ));
    }

    public function readAction($id)
    {
        $message = $this->getVisibleMessage($id);
        $this->markAsRead($message);

        return $this->redirect($this->get('request')->headers->get('Referer'));
    }

    public function deleteAction($id)
    {
        $message = $this->getVisibleMessage($id);

        $this->get('ornicar_message.object_manager')->remove($message);
        $this->get('ornicar_message.object_manager')->flush();

        return $this->redirectToInbox();
    }

    protected function redirectToInbox()
    {
        return $this->redirect($this->generateUrl('ornicar_message_message_list'));
    }

    protected function markAsRead(Message $message)
    {
        if(!$message->getIsRead()) {
            if($message->getTo()->is($this->get('security.context')->getUser())) {
                $this->get('ornicar_message.messenger')->markAsRead($message);
                $this->get('ornicar_message.object_manager')->flush();
            }
        }
    }

    protected function getVisibleMessage($id)
    {
        $user = $this->get('security.context')->getUser();
        $message = $this->get('ornicar_message.repository.message')->find($id);

        if(!$message) {
            throw new NotFoundHttpException('No such message');
        }
        if(!$message->isVisibleBy($user)) {
            throw new NotFoundHttpException('You shall not see this message');
        }

        return $message;
    }
}
