<?php
if (!defined('ROOT'))
define('ROOT',__DIR__);

if (!defined('DIR_VIEW'))
define('DIR_VIEW',__DIR__.DIRECTORY_SEPARATOR.'app/view/page/');

if (!defined('DIR_JS'))
define('DIR_JS',__DIR__.DIRECTORY_SEPARATOR.'assets/build/js/');

if (!defined('WEB'))
define('WEB','http://127.0.0.1:4000/');

if (!defined('ERROR_PAGE'))
define('ERROR_PAGE',DIR_VIEW.'error.php');

if (!defined('METHODS'))
define('METHODS',['get'=>1,'post'=>1,'put'=>1,'delete'=>1]);

if (!defined('LANGUAGES'))
define('LANGUAGES',['ru'=>'ru','en'=>'en']);

if (!defined('DEFAULT_LANG'))
define('DEFAULT_LANG', 'ru' );

if (!defined('DIR_LANGUAGES'))
define('DIR_LANGUAGES',ROOT .DIRECTORY_SEPARATOR.'app/language/library/');

if (!defined('FILE_PATTERNS_FIELD'))
define('FILE_PATTERNS_FIELD',ROOT .DIRECTORY_SEPARATOR.'patterns.php');

if (!defined('DIR_IMAGES'))
define('DIR_IMAGES',__DIR__.DIRECTORY_SEPARATOR.'images/');

if (!defined('IMAGE_USER'))
define('IMAGE_USER',__DIR__.DIRECTORY_SEPARATOR.'image.jpg');

if (!defined('SALT_USER_PAGE'))
define('SALT_USER_PAGE','salt');

if (!defined('DEV'))
define('DEV',1);

if (!defined('DB_USER_ROOT'))
define('DB_USER_ROOT','jeka');

if (!defined('DB_HOST'))
define('DB_HOST','127.0.0.1');

if (!defined('DB_PORT'))
define('DB_PORT',3306);

if (!defined('DB_NAME'))
define('DB_NAME','dbform');

if (!defined('ADMIN_EMAIl'))
define('ADMIN_EMAIl','email@gmail.ua');

if (!defined('ADMIN_NAME'))
define('ADMIN_NAME','Potap Potapenko');

if (!defined('SITE_NAME'))
define('SITE_NAME','FORM example');