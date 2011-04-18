<?php

namespace Ornicar\MessageBundle\Model;

use FOS\UserBundle\Model\User;

interface MessageRepositoryInterface
{
    function findRecentByUser(User $user, $asPaginator = false);

    function findRecentSentByUser(User $user, $asPaginator = false);

    function countUnreadByUser(User $user);

    function createNewMessage();
}
