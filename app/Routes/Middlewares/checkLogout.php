<?php

function checkLogout() {
    if (isset($_SESSION['userid']))
        return header('Location: ' . baseUrl('users/profile/' . initModel('users')->getUser($_SESSION['userid'])->guid));
}