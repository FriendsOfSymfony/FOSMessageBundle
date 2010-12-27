<?php

namespace Bundle\Ornicar\MessageBundle\Twig\Extension;

use Bundle\Ornicar\MessageBundle\Model\MessageRepositoryInterface;
use Symfony\Component\Security\SecurityContext;

class MessageExtension extends \Twig_Extension
{
    protected $messageRepository;
    protected $securityContext;

    protected $cache = array();

    public function __construct(MessageRepositoryInterface $messageRepository, SecurityContext $securityContext)
    {
        $this->messageRepository = $messageRepository;
        $this->securityContext = $securityContext;
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getGlobals()
    {
        return array(
            'fn_new_messages'  => new \Twig_Function($this, 'countNewMessages')
        );
    }

    public function countNewMessages()
    {
        if(array_key_exists('new_messages', $this->cache)) {
            return $this->cache['new_messages'];
        }
        $user = $this->securityContext->getUser();
        if(!$user) {
            return 0;
        }

        $nb = $this->messageRepository->countUnreadByUser($user);

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
