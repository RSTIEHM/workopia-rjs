<?php

namespace Framework;

use Framework\Session;

class Authorization
{
    // CHECK IF CURRENT LOGGED IN OWNS RESOURCE
    public static function isOwner($resourceID)
    {
        $sessionUser = Session::get("user");
        if ($sessionUser !== null && isset($sessionUser["id"])) {
            $sessionUserID = (int) $sessionUser["id"];
            return $sessionUserID === $resourceID;
        }
        return false;
    }
}
