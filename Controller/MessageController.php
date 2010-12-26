<?php

namespace Bundle\Ornicar\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MessageController extends Controller
{
    public function listAction()
    {
        $user = $this->get('security.context')->getUser();
        $messages = $this->get('ornicar_message.repository.message')->findRecentByUser($user);

        return $this->render('MessageBundle:Message:list.twig', array(
            'messages' => $messages
        ));
    }
}
