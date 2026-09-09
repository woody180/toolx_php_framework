<?php

function checkAuth(array $privilegies = []) {

    if (!DATABASE) return false;

    $id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;
    $user = null;

    if ($id) {

        $user = initModel('users')->getUser($id);

        if (is_null($user)) unset($_SESSION['userid']);

        if (!empty($privilegies)) {
            if (!is_null($user->usergroups) && in_array($user->usergroups->id, $privilegies) || !is_null($user->groups) && in_array($user->groups->id, $privilegies)) return true;
            return false;
        }

        if (!$user) {
            unset($_SESSION['userid']);
            return false;
        } else {
            return TRUE;
        }
    }

    return false;
    
}


function isGuid($id) {
    if (is_null($id) || empty($id)) return false;
    return preg_match('/^[a-f0-9]{32}$/i', $id);
}