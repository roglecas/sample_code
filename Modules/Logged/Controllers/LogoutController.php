<?php namespace DevApp\Modules\Logged\Controllers;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *    ---------------------------
 *     LogoutController.php
 * ----------------------------------------------------
 */

use DevApp\Application\Session;

class LogoutController extends LoggedController
{ 
    public function index() : void
    {
        Session::destroy();
        
        $this->redirect();
    }
}
