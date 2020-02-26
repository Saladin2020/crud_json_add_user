<?php

require_once './core/constant.php';
require_once './core/blueprint.php';

function __autoload($class_name) {
    $directorys = array(
        'core/',
        'workspace/',
    );
    foreach ($directorys as $directory) {
        if (file_exists($directory . $class_name . '.php')) {
            require_once($directory . $class_name . '.php');
            return;
        }
    }
}

switch ((isset($_GET["page"]) != '') ? $_GET["page"] : '') {
    case 'login':
        routepage::login();
        break;
    case 'logout':
        routepage::logout();
        break;
    case 'add_user':
        routepage::add_user();
        break;
     case 'edit_user':
        routepage::edit_user();
        break;
    case 'get_users':
        routepage::get_users();
        break;
    case 'remove_user':
        routepage::remove_user();
        break;
    case 'activate_user':
        routepage::activate_user();
        break;
    default:
        echo "no page";
        break;
}



