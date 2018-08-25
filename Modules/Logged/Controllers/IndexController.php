<?php namespace DevApp\Modules\Logged\Controllers;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *    ---------------------------
 *       LoginController.php
 * ----------------------------------------------------
 */

class IndexController extends LoggedController
{   
    public function __construct()
    {
        parent::__construct( 'Index' );
        $this->getAccessController();
        
        $this->view->assign('title', 'Welcome to Dev App');
    }
    
    /**
     * Index.
     * 
     * @return void.
     */
    public function index() : void
    { 
        $this->view->render(); 
    }
}
