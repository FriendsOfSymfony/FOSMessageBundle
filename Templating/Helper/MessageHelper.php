<?php

namespace Bundle\Ornicar\MessageBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;

class MessageHelper extends Helper
{
    public function getName()
    {
        return 'ornicar_message';
    }
}
