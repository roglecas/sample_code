<?php namespace DevApp;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *       ------------
 *         index.php
 * ----------------------------------------------------
 */

use Exception;
use DevApp\Application\Session;
use DevApp\Application\Request;
use DevApp\Application\Bootstrap;
    
define( 'DS',       DIRECTORY_SEPARATOR );
define( 'ROOT',     realpath( dirname( __FILE__ )) . DS );
define( 'APP_PATH', ROOT . 'Application' . DS );

try
{   
    require_once APP_PATH   . 'Local.php';
    require_once APP_PATH   . 'Config.php';
    require_once APP_PATH   . 'Autoload.php';
    require_once VENDOR_DIR . 'autoload.php';
        
    Session::init();
    Bootstrap::run( new Request );
}
catch ( Exception $e )
{
    echo $e->getMessage();
}
