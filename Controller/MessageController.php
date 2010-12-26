<?php

namespace Bundle\Ornicar\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
}
