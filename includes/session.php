<?php

session_start();

function setUserSession($user) {
    $_SESSION['user'] = $user;
}

function getUserSession() {
    return $_SESSION['user'] ?? null;
}

function destroySession() {
    session_destroy();
}