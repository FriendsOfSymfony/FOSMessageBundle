<?php

namespace Ornicar\MessageBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Form\Form;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Ornicar\MessageBundle\Model\Message;
use Ornicar\MessageBundle\Model\Composition;

class MessageController extends Controller
{
    public function newAction()
    {
        $form = $this->get('ornicar_message.form.message');
        $form['to']->setData($this->get('request')->query->get('to'));

        return $this->render('OrnicarMessageBundle:Message:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function createAction()
    {
        $form = $this->get('ornicar_message.form.message');
        $handler = $this->get('ornicar_message.form.message.handler');
        $message = $this->get('ornicar_message.model.factory')->createComposition();
        $message->setFrom($this->get('security.context')->getToken()->getUser());
        
        if ($handler->process($message)) {
            $this->get('session')->setFlash('ornicar_message_message_create', 'success');
            $this->get('ornicar_message.object_manager')->flush();
            return $this->redirect($this->generateUrl('ornicar_message_message_sent'));
        }
        
        return $this->render('OrnicarMessageBundle:Message:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function listAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $messages = $this->get('ornicar_message.repository.message')->findRecentByUser($user, true);
        $messages->setCurrentPageNumber($this->get('request')->query->get('page', 1));
        $messages->setItemCountPerPage($this->container->getParameter('ornicar_message.paginator.messages_per_page'));
        $messages->setPageRange(5);

        return $this->render('OrnicarMessageBundle:Message:list.html.twig', array(
            'messages' => $messages,
            'pagerUrl' => $this->get('router')->generate('ornicar_message_message_list')
        ));
    }

    public function sentAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $messages = $this->get('ornicar_message.repository.message')->findRecentSentByUser($user, true);
        $messages->setCurrentPageNumber($this->get('request')->query->get('page', 1));
        $messages->setItemCountPerPage($this->container->getParameter('ornicar_message.paginator.messages_per_page'));
        $messages->setPageRange(5);

        return $this->render('OrnicarMessageBundle:Message:sent.html.twig', array(
            'messages' => $messages,
            'pagerUrl' => $this->get('router')->generate('ornicar_message_message_sent')
        ));
    }

    public function showAction($id)
    {
        $message = $this->getVisibleMessage($id);
        $this->markAsRead($message);
        if ($message->getTo()->isUser($this->get('security.context')->getToken()->getUser())) {
            $form = $this->get('ornicar_message.form.message');
            $answer = $this->get('ornicar_message.model.factory')->createAnswer($message);
            
            $form->setData($answer);
        } else {
            $form = null;
        }
        
        return $this->render('OrnicarMessageBundle:Message:show.html.twig', array(
            'message' => $message,
            'form' => $form->createView(),
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
        if (!$message->getIsRead()) {
            if ($message->getTo()->isUser($this->get('security.context')->getToken()->getUser())) {
                $this->get('ornicar_message.messenger')->markAsRead($message);
                $this->get('ornicar_message.object_manager')->flush();
            }
        }
    }

    protected function getVisibleMessage($id)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $message = $this->get('ornicar_message.repository.message')->find($id);

        if (!$message) {
            throw new NotFoundHttpException('No such message');
        }
        if (!$message->isVisibleBy($user)) {
            throw new NotFoundHttpException('You shall not see this message');
        }

        return $message;
    }
}
