<?php

namespace Bundle\Ornicar\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MessageController extends Controller
{
    public function listAction()
    {
        $user = $this->get('security.context')->getUser();
        $messages = $this->get('ornicar_message.repository.message')->findRecentByUser($user, true);

        $page = $this->get('request')->query->get('page', 1);
        $messages->setCurrentPageNumber($page);
        $messages->setItemCountPerPage($this->container->getParameter('ornicar_message.paginator.messages_per_page'));
        $messages->setPageRange(5);

        return $this->render('MessageBundle:Message:list.twig', array(
            'messages' => $messages
        ));
    }

    public function showAction($id)
    {
        $message = $this->getVisibleMessage($id);

        if(!$message->getIsRead()) {
            $message->setIsRead(true);
            $this->get('ornicar_message.object_manager')->flush();
        }

        return $this->render('MessageBundle:Message:show.twig', array(
            'message' => $message
        ));
    }

    public function readAction($id)
    {
        $message = $this->getVisibleMessage($id);

        if(!$message->getIsRead()) {
            $message->setIsRead(true);
            $this->get('ornicar_message.object_manager')->flush();
        }

        return $this->redirect($this->get('request')->headers->get('Referer'));
    }

    public function deleteAction($id)
    {
        $message = $this->getVisibleMessage($id);

        $this->get('ornicar_message.object_manager')->remove($message);
        $this->get('ornicar_message.object_manager')->flush();

        return $this->redirect($this->generateUrl('ornicar_message_message_list'));
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
