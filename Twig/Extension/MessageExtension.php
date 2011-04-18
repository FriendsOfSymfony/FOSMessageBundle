<?php

namespace Ornicar\MessageBundle\Twig\Extension;

use Ornicar\MessageBundle\Messenger;
use Symfony\Component\Security\Core\SecurityContext;

class MessageExtension extends \Twig_Extension
{
    protected $messenger;
    protected $securityContext;

    protected $cache = array();

    public function __construct(Messenger $messenger, SecurityContext $securityContext)
    {
        $this->messenger = $messenger;
        $this->securityContext = $securityContext;
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getFunctions()
    {
        return array(
            'new_messages'  => new \Twig_Function_Method($this, 'countNewMessages')
        );
    }

    public function countNewMessages()
    {
        if(array_key_exists('new_messages', $this->cache)) {
            return $this->cache['new_messages'];
        }
        $token = $this->securityContext->getToken();
        if(!$token) {
            return 0;
        }
        $user = $token->getUser();
        if(!$user) {
            return 0;
        }

        $nb = $this->messenger->countUnreadByUser($user);

        return $this->cache['new_messages'] = $nb;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'ornicar_message';
    }
}
