<?php

namespace Ornicar\MessageBundle\Form;

use Ornicar\MessageBundle\Messenger;

use Symfony\Component\Form\Form;
use Ornicar\MessageBundle\Model\Message;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class MessageFormHandler
{
    
    protected $form;
    protected $request;
    protected $messenger;
    protected $userManager;
    
    public function __construct(Form $form, Request $request, Messenger $messenger, UserManagerInterface $userManager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->messenger = $messenger;
        $this->userManager = $userManager;
    }
    
    public function process(Message $message)
    {
        $this->form->setData($message);
        
        if ('POST' === $this->request->getMethod()) {
            $data = $this->request->get($this->form->getName(), array());
            $user = $this->userManager->findUserByUsername($data['to']);
            
            $this->form->bind(array_merge(
                $data,
                array('to' => $user)
            ));
            
            if ($this->form->isValid()) {
                $this->messenger->send($this->form->getData());
                
                return true;
            }
        }
        
        return false;
    }
    
}