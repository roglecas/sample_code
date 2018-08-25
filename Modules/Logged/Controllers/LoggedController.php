<?php namespace DevApp\Modules\Logged\Controllers;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *    ------------------------
 *     LoggedController.php
 * ----------------------------------------------------
 */

use DevApp\Application\Controller;

abstract class LoggedController extends Controller
{    
    public function __construct($app = false)
    {
        $this->getAccessController();
        
        parent::__construct( $app );
    }
}