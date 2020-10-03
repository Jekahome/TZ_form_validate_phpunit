<?php
declare(strict_types=1);

namespace app;


require_once('define.php');

if (DEV) {
    ini_set('display_errors', 'On');
    ini_set('display_startup_errors', 'On');
    ini_set('error_reporting', '-1');
    ini_set('log_errors', 'On');
}
ini_set('session.save_path', ROOT . '/tmp');
ini_set('session.name',  'FORM');
ini_set('session.use_cookies',  '1');

require_once('vendor/autoload.php');

!session_id() ? session_start() : false;
 \app\language\language::init()->initStore($_SESSION,$_COOKIE,$_SERVER['HTTP_ACCEPT_LANGUAGE']);

// Routing
try{
    new Route();
}catch (\Throwable $e){
    echo $e->getFile();
}

// select token from users where email='evgen@mail.ua';
// http://127.0.0.1:4000/?token=4e56c7119df704ca7dd6d2a7dda59399
return;



