<?php namespace DevApp\Application;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *     ------------------
 *          View.php
 * ----------------------------------------------------
 */

use Smarty;
use DevApp\Application\Error;
use DevApp\Application\Model;
use DevApp\Application\Session;

/**
 * Class View.
 * 
 */
class View extends Smarty
{
    private $js;
    private $model;
    private $module;
    private $plugin;
    private $tplView;

    public function __construct(Request $request)
    {
        parent::__construct();
        
        $this->js     = [];
        $this->plugin = [];
        $this->model  = new Model();
        $this->module = $request->getModule();
        $this->tplView = 'App' . DS . 'GenericViewError';
        
        $this->setCacheDir   ( ROOT      . 'Tmp'    . DS . 'cache'        . DS );
        $this->setCompileDir ( ROOT      . 'Tmp'    . DS . 'template'     . DS );
        $this->setTemplateDir( VIEW_PATH . 'Layout' . DS . DEFAULT_LAYOUT . DS );
        $this->setTemplateDir( VIEW_PATH . 'Layout' . DS . DEFAULT_LAYOUT . DS . 'configs' . DS, true );
    }

    /**
     * Render View.
     * 
     * @param string $view Optional, View name.
     * @return void.
     */
    public function render($view = false) : void
    {   
        $menu = false;
        $view = ! $view ? $this->tplView : $view;
        
        /**
         * Set application menu only for authenticate users.
         */
        if ( Session::getVar( 'auth' ))
        {
            $module = $this->model->getAppMenuModules( 'menu_order' );
            $menu   = $this->getMenu( $module );
        }
        
        $js       = count( $this->js )     ? $this->js     : [];
        $jsPlugin = count( $this->plugin ) ? $this->plugin : [];
        
        /**
         * Set parameters values for all views.
         */
        $vParameters = [
            'js'       => $js,
            'urlPath'  => URL_PATH,
            'jsPlugin' => $jsPlugin,
            'mainMenu' => $menu,
            'configs'  => [ 'app_name' => DEVAPP_NAME, 'app_company' => DEVAPP_COMPANY ],
            'pathCSS'  => URL_PATH . VIEW_DIR . '/' . LAYOUT_DIR . '/' . DEFAULT_LAYOUT . '/' . CSS_DIR . '/',
            'pathIMG'  => URL_PATH . VIEW_DIR . '/' . LAYOUT_DIR . '/' . DEFAULT_LAYOUT . '/' . IMG_DIR . '/',
            'pathJS'   => URL_PATH . VIEW_DIR . '/' . LAYOUT_DIR . '/' . DEFAULT_LAYOUT . '/' . JS_DIR  . '/',
        ];
        
        /**
         * Set path view variable.
         */
        $pathView = VIEW_PATH . ( strstr($view, 'App' ) ? $view . '.tpl' : $this->module . DS . $view . '.tpl' );
        
        /**
         * Render error view with error details when view does not exits.
         */
        if ( ! is_readable( $pathView ))
        {
            Error::renderErrorHandleView([
                'user'  => 'View error code: 10. View not found. Please contact IT department.',
                'admin' => 'View Path: ' . $this->module . DS . $view . '.tpl'
            ]);
        }
        
        /**
         * Assign content to the view.
         */
        $this->assign( 'content', $pathView );
        $this->assign( 'layout',  $vParameters );
        
        /**
         * Render the default template.
         */
        $this->display( 'template.tpl' );
    }

    /**
     * Set JS file.
     * 
     * @param array $javascript JS file name.
     * @return void.
     */
    public function setJsFile( array $javascript ) : void
    {  
        /**
         * Iterate each Javascript array element to load multiple JS files.
         * array_filter function cleans the array in case false is sent.
         */
        foreach ( array_filter( $javascript ) as $js )
        {
            $jsFile = URL_PATH . VIEW_DIR . '/' . $this->module . '/' . JS_DIR . '/' . $js . '.js';
            
            $curl = $this->checkUrlFile( $jsFile );
            
            /**
             * Render error view when Javascript does not exist.
             */
            if ( ! $curl )
            {
                Error::renderErrorHandleView([
                    'user'  => 'View error code: 14. Javascript not found. Please contact IT department.',
                    'admin' => 'File: ' . $js . '.js'
                ]);
                        
                return;
            }
		
            $this->js[] = $jsFile;
        }
    }

    /**
     * Set JS Plugging.
     * 
     * @param string $jsPlugin Plugging name.
     * @return void.
     */
    public function setJsPlugin( array $jsPlugin ) : void
    {   
        foreach ( $jsPlugin as $js )
        {
            $jsPlgn = URL_PATH . PUBLIC_DIR . '/' . JS_DIR . '/' . $js . '.js';
            
            $curl = $this->checkUrlFile( $jsPlgn );
            
            if ( ! $curl )
            {
                Error::renderErrorHandleView([
                    'user'  => 'View error code: 18. Javascript plugin not found. Please contact IT department.',
                    'admin' => 'Plugin: ' . $js
                ]);
            }
			
            $this->plugin[] = $jsPlgn;
        }
    }
    
    /**
     * Check URL File.
     * 
     * Check if any file on specific URL exist.
     * 
     * @param string $url URL file to check.
     * @return bool.
     */
    private function checkUrlFile(string $url) : bool
    {         
        $curl = curl_init();
        curl_setopt_array( $curl, [ 
            CURLOPT_URL => $url, 
            CURLOPT_HEADER => 1, 
            CURLOPT_NOBODY => 1, 
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => 0 
        ]);
        
        if ( ! curl_exec( $curl ))
        { 
            Error::renderErrorHandleView([
                'user'  => 'View error code: 20. CURL Error: ' . curl_error( $curl ) . ' - Code: ' . curl_errno( $curl ) . '.'
            ]); 
        }
        
        $header = curl_exec( $curl );
        
        curl_close( $curl );
        
        return strpos( $header, '/javascript' );
    }
    
    /**
     * Set Template view.
     * 
     * @param string $view Template name.
     * @return void.
     */
    public function setTemplate(string $view) : void
    {
        $this->tplView = $view;
    }
    
    /**
     * Set Template view.
     * 
     * @return string.
     */
    public function getTemplate() : string
    {
        return $this->tplView;
    }
    
    /**
     * Get menu and sub menus by user access.
     * 
     * @param array $module Array with module access.
     * @retunr array.
     */
    private function getMenu(array $module) : array
    {
        $mmCont   = 0;
        $smCont   = 0;
        $mainMenu = [];

        /**
         * Get menu and sub menus by user access.
         */
        foreach ( $module as $m )
        {   
            if ( Session::getVar(strtolower( $m['MODULE_PREFIX'] ) . 'M' ) > 0 )
            {   
                $mmCont ++;
                $menu                = $this->model->getAppMenuByModuleId( $m['MODULE_ID'] );
                $mainMenu[ $mmCont ] = [ 'icon' => $m['MODULE_ICON'], 'title' => $m['MODULE_NAME'], 'subMenu' => [] ];
                    
                foreach ( $menu as $me )
                {
                    if ( Session::getVar(strtolower( $m['MODULE_PREFIX'] ) . 'M' ) & ( int ) $me['MENU_ACCESS'] )
                    {
                        $mainMenu[ $mmCont ]['subMenu'][ $smCont ] = [
                            'title' => $me['MENU_NAME'],
                            'link'  => URL_PATH . $m['MODULE_URL'] . '/' . $me['MENU_URL']
                        ];
                          
                        $smCont ++;
                    }
                }
                  
                $smCont = 0;
            }
        }
        
        return $mainMenu;
    }
}