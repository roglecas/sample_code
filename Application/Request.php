<?php namespace DevApp\Application;

/*
 * ----------------------------------------------------
 *  Dev Project | Rolando Gonzalez
 *  --------------------------------------
 *          Request.php
 * ----------------------------------------------------
 */

class Request
{
    private $module;
    private $method;
    private $arguments;
    private $controller;
     
    public function __construct()
    {   
        /**
         * Set default values.
         */
        $this->module     = false;
        $this->method     = false;
        $this->arguments  = false;
        $this->controller = false;
        
        if( isset( $_GET['url'] ) )
        {
            /**
             * Get and sanitize whole URL string.
             */
            $url = filter_input( INPUT_GET, 'url', FILTER_SANITIZE_URL );
          
            /**
             * Set session variable with URL in case the user has no valid 
             * login session store the URL and after user login redirect 
             * to the previous URL entered.
             */
            SESSION::setVar( 'appURLRqt', $url );
            
            /**
             * Split URL.
             */
            $url = explode( '/', $url );
            
            /**
             * Get and remove first element of URL array that is the module name.
             */
            $this->module = $this->getFromUrl( array_shift( $url ));
            
            /**
             * Next array element will be the controller name. Get and remove from the array.
             */
            $this->controller = $this->getFromUrl(array_shift($url));
            
            /**
             * Next array element will be the method name. Get and remove from the array.
             */
            $this->method = $this->getFromUrl(array_shift($url));
            
            /**
             * Everything else on the array will be use as method arguments.
             */
            $this->arguments = $url;
        }
        
        /**
         * Get default names if no values was passed by the URL.
         */
        if( ! $this->module )     { $this->module     = DEFAULT_MODULE; }
        if( ! $this->method )     { $this->method     = DEFAULT_METHOD; }
        if( ! $this->arguments )  { $this->arguments  = []; }
        if( ! $this->controller ) { $this->controller = DEFAULT_CONTROLLER; }
    }
    
    /**
     * Get from URL.
     * 
     * If a combine word by a dash exist in the url segment,
     * removes the dash symbol and uppercase each first letter.
     * 
     * @param array $url URL portion.
     * @return string.
     */
    private function getFromUrl($url) : string
    {
        return implode('', array_map( 'ucfirst', explode( '-', $url )));
    }
    
    /**
     * Get Module.
     * 
     * @return string.
     */
    public function getModule() : string 
    { 
        return $this->module; 
    }
    
    /**
     * Get Method.
     * 
     * @return string.
     */
    public function getMethod() : string 
    { 
        return $this->method; 
    }
    
    /**
     * Get Arguments.
     * 
     * @return array.
     */
    public function getArguments() : array 
    { 
        return $this->arguments;     
    }
    
    /**
     * Get Controller.
     * 
     * @return string.
     */
    public function getController() : string 
    { 
        return $this->controller; 
    }
}