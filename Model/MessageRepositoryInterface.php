<?php

namespace Bundle\Ornicar\MessageBundle\Model;

use Bundle\FOS\UserBundle\Model\User;

interface MessageRepositoryInterface
{
    function findRecentUnreadByUser(User $user, $asPaginator = false);
}
