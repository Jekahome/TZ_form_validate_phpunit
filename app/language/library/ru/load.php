<?php

switch ($page) {
    case 'registration_field':
    case 'registration_errors':
    case 'message_field':
    case 'user':
    case 'users':
    case 'layout':
    case 'index':
    case 'mail':
    case 'update':
        return require DIR_LANGUAGES . 'ru/' . $page . '.php';
        break;
    default:
        return [];
        break;
}
