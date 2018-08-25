<?php namespace DevApp\Modules\Index\Controllers;

/**
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *    ---------------------------
 *     IndexController.php
 * ----------------------------------------------------
 */

use DevApp\Application\Session;
use DevApp\Application\Controller;

class IndexController extends Controller
{      
    public function __construct()
    {
        parent::__construct( 'Index' );
    }
    
    /**
     * Index.
     * 
     * @return void.
     */
    public function index()
    {
        $this->redirectLoggedUser(); 
        
        Session::setVar( 'auth', false );
        
        $this->view->render();
    }
    
    /**
     * Login User.
     * 
     * @return redirect.
     */
    public function login() : void
    { 
        $this->redirectLoggedUser();
        
        /**
         * Get and validate values.
         */
        $user = $this->validate->postText(   'loginUser' );
        $pass = $this->validate->postString( 'loginPass' );
        $lgn  = $this->model->getLoginUser( $user, $pass );
        
        /**
         * Check values.
         */
        $this->renderViewRule( ! $user, 'Invalid Username' );
        $this->renderViewRule( ! $pass, 'Invalid Password' );
        $this->renderViewRule( $lgn['LGN'] != 'Y', 'Access Denied. Please try again', false, 8, 2 );
        
        $this->model->setUserLog( $lgn['USERID'] );
        
        Session::setVar( 'auth',   true );
        Session::setVar( 'userId', $lgn['USERID'] );
        Session::setVar( 'userN',  $user );
        
        $this->redirect( 'logged' );
    }

    /**
     * Redirect User for specific permission.
     * 
     * @param string $url Optional, URL to redirect user.
     * @return redirect.
     */
    private function redirectLoggedUser() : void
    {
        if ( Session::getVar( 'auth' ))
        {
            $this->redirect( 'logged' );
        }
    }
    
    /**
     * Set Application notification.
     * 
     * Check if any notification for login user was set. 
     * and show on the top bar.
     * 
     * @return void.
     */
    public function appNotification()
    {
        echo json_encode( $this->model->getAppNotificationByUser( Session::getVar( 'userId' )));
    }
}