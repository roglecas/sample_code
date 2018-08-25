<?php namespace DevApp\Application;

/**
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 * --------------------------------------
 *         Controller.php
 * ----------------------------------------------------
 */

use DevApp\Application\View;
use DevApp\Application\Model;
use DevApp\Application\Request;
use DevApp\Application\Session;
use DevApp\Application\Validate;

/**
 * Class Controller.
 */
abstract class Controller extends Error
{
    /** 
     * @var View
     */
    public $view;
    
    /** 
     * @var Request
     */
    protected $request;
    
    /** 
     * @var Validate 
     */
    protected $validate;
    
    /** 
     * @var Model
     */
    protected $model;

    /**
     * Constructor.
     *
     * @param string $app Optional, Set the model, view and JS for the controller.
     * @retunr void.
     */
    public function __construct($app = false)
    {
        $this->request  = new Request();
        $this->validate = new Validate();
        $this->view     = new View( $this->request );

        $this->setAppSettings( $app );
    }

    /**
     * Abstract method to make sure all controllers have an index method.
     */
    abstract public function index();

   /* =====================================================================================================================================
    * =====================================================================================================================================
    *
    *             FRAMEWORK USER ACCESS CONTROL
    *
    * =====================================================================================================================================
    * =====================================================================================================================================
    */

    /**
     * Get Access Controller.
     *
     * Redirect to login page if authentication session variable is not set.
     *
     * @retunr void.
     */
    protected function getAccessController() : void
    {
        $this->redirectRule( ! Session::getVar( 'auth' ));
    }

    /**
     * Get access control.
     *
     * Check authentication session variable was not set redirect to the login page.
     *
     * Pass two parameter to this method, module and value
     * to check if specific user has access to specific module.
     *
     * Module name is store in the DB and the value to access this
     * specific module is store on the DB also.
     *
     * @param string $module Optional, Module name.
     * @param int    $value  Optional, Value that allow access to the module.
     * @return redirect.
     */
    protected function getAccessControl(string $module, int $value) : void
    {
        $this->redirectRule( !   Session::getVar( 'auth' ));
        $this->redirectRule( ! ( Session::getVar( $module ) & $value ), 'logged' );
    }

    /**
     * Get access to specific application.
     *
     * Check if the user has access to the application.
     * Module name is stored in the DB and the value to
     * access this specific module is also stored in the DB.
     *
     * @param string $module Module name.
     * @param int    $value  Value that allow access to the module.
     * @return bool.
     */
    protected function getAccess(string $module, int $value) : bool
    {
        return Session::getVar( $module ) & $value;
    }

    /**
     * Get module Access.
     *
     * Check if the user has access to specific module.
     *
     * @param string $module Module name.
     * @return void.
     */
    protected function redirectModuleAccess( string $module) : void
    {
        $this->redirectRule( ! Session::getVar( $module ) > 0, 'logged' );
    }

    /**
     * Get special level.
     *
     * Take module name and user access value
     * and validate if the user permissions
     * value can have access to an specific module.
     *
     * @param string $module Module Name.
     * @param int    $value  Value to check.
     * @return boolean.
     */
    protected function getSpecialLevel(string $module, int $value) : bool
    {
        if ( Session::getVar( $module ) & $value )
        {
            return true;
        }
    }

    /**
     * Redirect if user has no access.
     *
     * The method check if the user has permission to an specific
     * module. If not, redirect the user to specific page.
     *
     * @param string $module     Module Name.
     * @param int    $permission Permission value.
     * @param string $redirect   Redirect URL.
     * @return void.
     */
    protected function redirectIfNoAccess(string $module, int $permission, string $redirect) : void
    {
        Session::getVar( $module ) & $permission ? '' : $this->redirect( $redirect );
    }

    /* ====================================================================================================================================
    * =====================================================================================================================================
    *
    *             FRAMEWORK HELPERS
    *
    * =====================================================================================================================================
    * =====================================================================================================================================
    */

    /**
     * Set application settings.
     *
     * Check if default application module was set on 
     * controllers to set default model, view and JS file.
     * Also set redirect error message for redirect validation 
     * if any error is set and check if there any user notification
     * and show in the top bar.
     *
     * @param string $app Application name.
     * @return void.
     */
    private function setAppSettings(string $app) : void
    {
        $this->setModel(   $app );
        $this->setTplView( $app );
        
        $this->view->setJsFile([ $app ]);
        $this->setRedirectMsg();
        $this->setAppNotification();
    }

    /**
     * Set application notification.
     *
     * Check if any notification for login user was set
     * and show on the top bar.
     *
     * @return void.
     */
    private function setAppNotification() : void
    {
        if ( Session::getVar( 'userId' ))
        {
            $model = new Model();
            $msg   = $model->getAppNotificationByUser( Session::getVar( 'userId' ));

            ! empty( array_filter( $msg ) ) ? $this->view->assign( '_appNotification', $msg ) : '';
        }
    }

    /**
     * Set Redirect Message.
     *
     * Get a session variable that must be an array and set a smarty
     * variable with the name of the element key of the array and his value.
     *
     * Element name options are: Error, Info, Wrn.
     *
     * @return void.
     */
    private function setRedirectMsg() : void
    {
        $redirectMsg = Session::getVar( 'devAppRdrctMsg' );

        if ( $redirectMsg )
        {
            $keyName = key( $redirectMsg );

            $this->view->assign( 'devAppColMd',    4 );
            $this->view->assign( 'devAppColMdOff', 4 );
            $this->view->assign( $keyName, $redirectMsg[ $keyName ] );
            
            Session::destroy( 'devAppRdrctMsg');
        }
    }

    /**
     * Redirect.
     *
     * Redirect to specific path or domain URL.
     *
     * @param string $path Path after domain you want redirect.
     * @param string $msg  Optional. Error message for the view.
     * @param string $bck  Optional. Background name. Default Error, options Info, Wrn.
     * @return redirect.
     */
    protected function redirect($path = false, $msg = false, string $bck = 'Error') : void
    {
        if ( $msg )
        {
            Session::setVar( 'devAppRdrctMsg', ['_app' . $bck . 'Msg' => $msg] );
        }
        
        header( 'location:' . URL_PATH . $path);
        
        exit;
    }

    /**
     * Check Rule and Redirect.
     *
     * Get a $rule variable with specific validation and check for this validation.
     * In case does not pass the validation process redirect to a specific URL
     * otherwise do nothing.
     *
     * @param mixed  $rule Rule to validate.
     * @param string $url  Optional, URL to redirect.
     * @param string $msg  Optional, Error message to display.
     * @param string $bck  Optional. Background name. Default Error, options Info, Wrn.
     * @return void.
     */
    protected function redirectRule($rule, $url = false, $msg = false, string $bck = 'Error') : void
    {
        if ( $rule )
        {
            $this->redirect( $url, $msg, $bck );
        }
    }

    /**
     * Check Rule and Render View with Error.
     *
     * Get a $rule variable with specific validation and check for this validation.
     * In case does not pass the validation process render a specific view with
     * an error message otherwise do nothing.
     *
     * @param mixed  $rule     Rule to validate.
     * @param string $message  Optional. Error Message to display in case of validation error.
     * @param string $view     Optional, default _tplView set in the controller or name of the view to render in case of validation error.
     * @param int    $colMd    Optional, Number of column for medium and large screens.
     * @param int    $colMdOff Optional, Number of column to move for medium and large screens.
     * @return void.
     */
    public function renderViewRule($rule, string $message = 'Validation error.', $view = false, int $colMd = 4, int $colMdOff = 4) : void
    {
        if ( $rule )
        {
            $this->renderViewError( $message, $view, $colMd, $colMdOff );
        }    
    }

    /**
     * Return HTTP code if validate rule.
     *
     * @param string $rule Rule to validate.
     * @param int    $code Optional, HTTP code.
     * @param string $msg  Message to send.
     * @return redirect.
     */
    public function httpResponseRule($rule, $code = false, $msg = false) : void
    {
        if ( $rule ) 
        { 
            $this->httpResponse( $code, $msg );
        }
    }

    /**
     * Only response to local HTTP request.
     *
     * @param int    $code Optional, HTTP code.
     * @param string $msg  Optional, Message to send.
     * @return void.
     */
    public function httpLocalResponse(int $code = 400, $msg = false) : void
    {
        if ( $_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR'] )
        {
            $this->httpResponse( $code, $msg );
        }
    }

    /**
     * Return HTTP code.
     *
     * @param int    $code Optional, HTTP code.
     * @param string $msg  Message to send.
     * @return redirect.
     */
    public function httpResponse($code = false, $msg = false) : void
    {
        echo $msg;
        
        http_response_code( $code );
        
        exit;
    }

    /**
     * Get all menus by module name.
     *
     * @return void.
     */
    protected function getMenuByModule() : void
    {
        $mMenu = [];
        $model = new Model();
        $menu  = $model->getMenuByModuleName( $this->request->getModule() );

        if ( $menu )
        {
            foreach ( $menu as $m )
            {
                if ( Session::getVar( strtolower( $m['MODULE_PREFIX'] ) . 'M') & ( int ) $m['MENU_ACCESS'] )
                {
                    $mMenu[] = [ 'title' => $m['MENU_NAME'], 'link'  => URL_PATH . $m['MODULE_URL'] . '/' . $m['MENU_URL'] ];
                }
            }
        }

        $this->view->assign( 'mMenu', $mMenu );
        $this->view->render( 'App' . DS . 'ModuleMenu' );
    }

    /**
     * Set Model.
     *
     * Set specific model to a controller.
     *
     * @param sting $model Model name.
     * @return object a instance of the model class.
     */
    protected function setModel(string $model) : void
    {
        $model  = ucwords( $model ) . MODEL_NM;
        $module = $this->request->getModule();
        $model  = APP_NMS . DS_NMS  . MODULE_NMS . DS_NMS . ucfirst( $module ) . DS_NMS . MODEL_NMS . DS_NMS . $model;

        /**
         * Render error view with error details when model class does not exits.
         */
        if ( ! class_exists( $model ))
        {
            Error::renderErrorHandleView([
                'admin' => 'Model Class Name: ' . $model,
                'user'  => 'Controller error code: 12. Model Class not found. Please contact IT department.'
            ]);
        }

        $this->model = new $model;
    }

    /* ====================================================================================================================================
    * =====================================================================================================================================
    *
    *             GENERAL HELPERS
    *
    * =====================================================================================================================================
    * =====================================================================================================================================
    */

    /**
     * Is Empty.
     *
     * Give a variable check if is array or not and
     * return if the variable is an empty variable.
     *
     * @param mixed $var Regular or Array variable.
     * @return bool.
     */
    protected function isEmpty($var) : bool
    {
        return empty( ! is_array( $var ) ? $var : array_filter( $var ));
    }
    
    /**
     * Remove for empty array.
     * 
     * Return false for empty array.
     * 
     * @param array $var Array to check.
     * @return mixed.
     */
    protected function rmvEmpty(array $var)
    {
        return ! empty( array_filter( $var )) ? $var : false;
    }

    /**
     * Is Multidimensional Array.
     *
     * Check when array is multidimensional or not.
     *
     * @param array $array Array to check.
     * @return bool.
     */
    protected function isMultidimensionArray(array $array) : bool
    {
        return count( $array ) !== count( $array, COUNT_RECURSIVE );
    }

    /**
     * Set File Menu.
     *
     * Get an array to set the file menu. The array must have multiples elements key.
     *
     * menuTitle. String, Title to the link menu.
     * link.      String, Link to send the request to specific module and controller.
     * method.    String, Method to send the request to specific controller set in the link.
     * args.      Array,  Arguments to pass as a parameter to the method. Must be declare a func element.
     * imgName.   String, Image name in Image folder. Image must be PNG format.
     * icon.      String, Icon name to use in the menu link, if no image name was passed. Icon from fontawesome.
     * modalLink. String, Div id to display as a bootstrap modal. Does not allow arguments element.
     * cssClass.  String, Specific CSS class for that menu link to apply custom CSS style.
     * attr.      String, Specific attribute to anchor tag. Ex ['attr' => 'data-inv="' . $invId . '"''].
     * linkTitle. String, Title to the link in the anchor tag.
     *
     * @param array $array Array with menu values to set.
     * @return void.
     */
    protected function setFileMenu(array $array) : void
    {
        $this->view->assign( 'devAppFileMenu', $array );
    }

    /**
     * Set conditions to search in a Database.
     *
     * Get an array with key value pair to check if it is not empty and set the conditions to search in the DB.
     * $conditions array must have 3 mandatory elements and 4 optional.
     *
     * MANDATORY ELEMENTS.
     * Must contain 3 mandatory elements: "field", "compare" and "value".
     *
     * FIELD:
     *  Column name to set in the where clause. Example: ['field' => 'SSA.customer'].
     *  You can also use single row functions.  Example: ['field' => 'LOWER(SSA.customer)'].
     *  Also set conditional inside.            Example: ['field' => '(column IS NULL OR LOWER(other_column))'].
     *
     * COMPARE:
     *  Comparison type you want to use for the field previous set.
     *  Example: ['compare' => '='] | ['compare' => '>'] | ['compare' => 'LIKE'] | ['compare' => 'IN'].
     *
     * VALUE:
     *  The value you need to compare to the field. Example: ['value' => 2] | ['value' => "'%" . strtoupper('name') . "%'"].
     *  Value element could be an array.            Example: ['value' => [2, 4]] | ['value' => [2, 'string']].
     *
     *  Also a multidimensional array. To use this option you need to set the optional element "multidimension"
     *  with the value of the name of the element you want to take the value from.
     *
     *  Example:
     *  ['field' => 'rep', 'compare' => 'IN', 'value' => [['rep' = 12, 'name' => 'NAME'], ['rep' => 14, 'name' => 'NAME']], 'multidimension' => 'rep'].
     *
     *  Result:
     *  AND rep IN (12, 14).
     *
     * OPTIONAL ELEMENTS.
     *  Can be added 4 optional elements: "boolean", "custom", "multidimension" and "quote".
     *
     * BOOLEAN:
     *  This array element is the first to check in the array. If is set and true then will build the condition string.
     *
     *  Example:
     *  A good example is for select (drop down) menu with 2 options of value Include (1) and Exclude (0). Then you can add to the array:
     *
     *  array('boolean' => SELECTED_MENU, 'field' => 'SSA.package', 'compare' => '=', 'value' => 'EQP', quote => true).
     *
     *  If the user select Include (1) from the menu returns. " AND SSA.package = 'EQP' " since boolean was set and equal true.
     *
     *  Another example with custom statement. array('boolean' => SELECTED_MENU, 'custom' => "SSA.package = 'EQP'").
     *
     * CUSTOM:
     *  To skip the construction of the condition and set a custom condition. This option is check before condition build process.
     *
     *  If is set everything else is skipped.
     *  Example:
     *  array('custom' => 'AND (field_name = value OR other_field = other_value)').
     *
     * MULTIDIMENSION
     *   To use in multidimensional arrays. To get one specific element to set your condition set the name of the element.
     *
     *   Example:
     *   array('field' => 'rep', 'compare' => 'IN', 'value' => array(array('rep' = 12, 'name' => 'NAME'), array('rep' => 14, 'name' => 'NAME')), 'multidimension' => 'rep').
     *
     *   Result:
     *   AND rep IN (12, 14).
     *
     * QUOTE
     *  Whenever you want to quote values in where clause when comparing data.
     *  Example: array('quote' => true, 'field' => 'rep', 'compare' => 'IN', 'value' => array(array('rep' => 12, 'name' => 'NAME_1'), array('rep' => 14, 'name' => 'NAME_2')), 'multidimension' => 'name').
     *  Result: AND rep IN ('NAME_1', 'NAME_2').
     *
     * @param  array  $conditions  conditions to check and set. Mandatory elements names. See description method for details.
     * @param  string $front       Optional, Default value is "AND". The condition to place in the front (start) of the query clause.
     * @param  string $view        Optional, View to render if errors, by default take _tplView variable set in the controller.
     * @param  bool   $redirect    Optional, Redirect or Render.
     * @param  int    $colMd       Optional, Number of column for medium and large screens.
     * @param  int    $colMdOff    Optional, Number of column to move for medium and large screens.
     * @return string with a conditions to search in a DB.
     */
    protected function setDbConditions(array $conditions, string $front = ' AND ', $view = false, $redirect = false, int $colMd = 4, int $colMdOff = 4) : string
    {
        $cond = [];

        foreach ( $conditions as $c )
        {
            /**
             * Discard.
             */
            if ( array_key_exists( 'boolean', $c ) && empty( $c['boolean'] )) {  continue; }
            if ( array_key_exists( 'custom',  $c )) { $cond[] = $c['custom'];    continue; }

            /**
             * Must contain 3 mandatory elements: "field", "compare" and "value".
             */
            if ( ! array_key_exists( 'field', $c ) || ! array_key_exists( 'compare', $c ) || ! array_key_exists( 'value', $c ))
            {
                $redirect ? $this->redirect( $view ) : $this->renderViewError( 'Mandatory condition elements missing.', $view, $colMd, $colMdOff );
            }

            /**
             * Discard.
             */
            if ( empty( $c['field'] )) { continue; }

            /**
             * Multidimensional array with array in value.
             */
            if ( is_array( $c['value'] ) && isset( $c['multidimension'] ))
            {
                $value = "('" . implode( "', '", array_column( $c['value'], $c['multidimension'] )) . "')";
            }
            
            /**
             * String value.
             */
            elseif ( is_array($c['value'] ))
            {
                $value = "('" . implode( "', '", $c['value'] ) . "')";
            }
            
            /**
             * Everything else.
             */
            else
            {
                $value = isset( $c['quote'] ) ? "'" . $c['value'] . "'" : $c['value'];
            }

            /**
             * Store condition in temporary array variable.
             */
            $cond[] = empty( $c['value'] ) ? '' : $c['field'] . ' ' . $c['compare'] . ' ' . $value;
        }

        return count( array_filter( $cond )) ? ' ' . $front . ' ' . implode( ' AND ', array_filter($cond )) : '';
    }

    /**
     * Render a view for empty conditions.
     *
     * Check if a array or a variable is empty and if is true render a
     * specific view with an error message.
     *
     *
     * @param array|string $condition Variable to check.
     * @param string       $message   Error message to show on the view.
     * @param string       $view      Optional, Default _tplView set on each controller or view Name to render.
     * @param int          $colMd     Optional, Number of column for medium and large screens.
     * @param int          $colMdOff  Optional, Number of column to move for medium and large screens.
     * @return void.
     */
    protected function renderViewForEmptyVariable($condition, string $message = 'No Data Found', $view = false, int $colMd = 4, int $colMdOff = 4) : void
    {
        $cond = ! is_array( $condition ) ? $condition : array_filter( $condition );

        if ( empty( $cond ))
        {
            $this->renderViewError( $message, $view, $colMd, $colMdOff );
        }
    }

    /**
     * Render a view with Error message.
     *
     * Use to render a specific view with some custom message. Use columns width and columns move
     * for medium and large screens. those are optional arguments by default is 4 columns.
     *
     *
     * @param string $message  Error message to show on the view.
     * @param string $view     View Name to render.
     * @param int    $colMd    Optional, Number of column for medium and large screens.
     * @param int    $colMdOff Optional, Number of column to move for medium and large screens.
     * @return void.
     */
    protected function renderViewError(string $message = 'We found an Error. Please try again', $view = false, int $colMd = 4, int $colMdOff = 4) : void
    {
        $view = ! $view ? $this->view->getTemplate() : $view;

        $this->view->assign( '_appErrorMsg',   $message  );
        $this->view->assign( 'devAppColMd',    $colMd    );
        $this->view->assign( 'devAppColMdOff', $colMdOff );
     
        $this->view->render( $view );
        
        exit;
    }

    /**
     * Redirect to a page for empty conditions.
     *
     * Check if a array or a variable if empty and
     * if is true redirect to a specific page.
     *
     *
     * @param array|string $condition Variable to check.
     * @param string       $page      Page to redirect, just the portion after URL.
     * @param string $msg  Optional, Error message to display.
     * @param string $bck  Optional. Background name. Default Error, options Info, Wrn.
     * @return void.
     */
    protected function redirectForEmptyVariable($condition, string $page, $msg = false, string $bck = 'Error') : void
    {
        $cond = is_array( $condition ) ? array_filter( $condition ) : $condition;

        if ( empty( $cond )) 
        { 
            $this->redirect( $page, $msg, $bck ); 
        }
    }

    /**
     * Set template view.
     *
     * @param string $view View name.
     * @return void.
     */
    protected function setTplView(string $view) : void
    {
        $this->view->setTemplate( $view );
    }

    /**
     * Validate.
     *
     * This method validate POST or value variable and render, redirect or throw HTTP code
     * depend what was set. Use in certain validation scenarios only. See details bellow.
     * 
     * This method return indexed and associative array for successful validation for Post
     * validation. For variable validation in order to get an associative array you must pass
     * the name in the array. See NAME Option for more details.
     *
     * ----------------------------------------------------------------
     *    2 MANDATORY ARRAY ELEMENTS
     * ----------------------------------------------------------------
     * 
     * Name: VAR
     * Type: String | Mixed.
     * Desc: Value or Post variable name. 
     * Ex:   ['var' => 'year'] | ['var' => $year]; $_POST['year'] or Variable validation.
     *
     * Name: RULE
     * Type: String.
     * Desc: Valid method in Validate Class. For POST validation this method adds automatically 'post' in front the rule.
     * Ex:   ['rule' => 'int'].
     *
     * ---------------------------------------------------------------
     *    7 OPTIONAL ARRAY ELEMENTS
     * ---------------------------------------------------------------
     * 
     * Name: TYPE
     * Type: String
     * Desc: HTTP request method. 
     * Ex:   ['type' => 'PUT']; ['type' => 'DELETE']; ['type' => 'POST'];
     * 
     * Name: CSTM
     * Type: String.
     * Desc: Custom validation rule like int comparison.
     * Ex:   ['cstm' => ' > ' . date('Y')];
     * 
     * Name: MSG
     * Type: Stirng.
     * Desc: Fail message to display if validation fails.
     * Ex:   ['msg' => 'Invalid Value'];
     *
     * Name: URL
     * Type: String.
     * Desc: URL module/controller/method/arguments to redirect if validation fails.
     * Ex:   ['url' => 'accounting/transaction-account'];
     * 
     * Name: HTTP
     * Type: Int.
     * Desc: HTTP error code to throw if validation fails.
     * Ex:   ['http' => 400]; (Bad Request).
     * 
     * Name: OPT
     * Type: Bool.
     * Desc: Optional validation, it is no mandatory unless user send a value this validation will set.
     * Ex:   ['opt' => true];
     * 
     * Name: NAME
     * Type: String.
     * Desc: If variable validation wants to return var name in the return array set this element ith the name to return.
     * Ex:   $v = ['name' => 'year']; | $year = $v['year']; $year = $v[0]; 
     * 
     * ----------------------------------------------------------
     *    VALIDATION EXAMPLES
     * ----------------------------------------------------------
     * 
     * - Render a specific view if user send any POST value and validation fail.
     * -------------------------------------------------------------------------
     * RAW VALIDATE: 
     * $y = $this->validate->postInt('year');     
     * $this->renderViewRule(!empty($_POST['year']) && !$y && $y > date('Y'), 'Invalid year');
     * 
     * THIS METHOD:  
     * ['var' => 'year', 'rule' => 'int', 'cstm' => ' > ' . date('Y'), 'msg' => 'Invalid year', 'opt' => true]
     *
     * - Render a specific view if variable validation fail.
     * -----------------------------------------------------
     * RAW VALIDATE:
     * $int = $this->validate->int($int);     
     * $this->renderViewRule(!$int && $int > 10, 'Invalid input');
     * 
     * THIS MEHTOD:
     * ['var' => $int, 'rule' => 'int', 'cstm' => '> 10', 'msg' => 'Invalid input']
     *
     * - Redirect to specific controller if POST validation fail.
     * ----------------------------------------------------------
     * RAW VALIDATE:
     * $int = $this->validate->int($int);     
     * $this->redirectRule(!$int && $int > 10, 'module/controller', 'Invalid input');
     * 
     * - AJAX call make sure HTTP request method is the one you are waiting for.
     * -------------------------------------------------------------------------
     * RAW VALIDATE:
     * $int  = $this->validate->int($int);
     * $mthd = 'DELETE'
     * $type = $this->validate->serverString('REQUEST_METHOD');
     * $this->httpResponseRulu(!$int && $mthd != $type, 400);
     * 
     * THIS METHOD:
     * ['var' => $int, 'rule' => 'int', 'http' => 400, 'type' => 'delete'];
     *
     * ----------------------------------------
     *   NOTE
     * ----------------------------------------
     * If need to apply a function to the custom validation do not use this method choose other validation logic instead.
     * 
     * DO NOT USE FOR THIS CASE.
     * ------------------------
     * ['var' => $string, 'rule' => 'text', 'cstm' => strlen($string) > 5, 'Invalid input']
     * 
     * USE INSTEAD RAW VALIDATE:
     * $s = $this->validate->text($string);     
     * $this->renderViewRule(!$s && strlen($s) > 5, 'Invalid input');
     *
     * @param  array $validate Array with validation rules.
     * @return array with validation values.
     */
    protected function validate(array $validate) : array
    {
        /**
         * Declare key and array to return.
         */
        $rKey   = 0;
        $return = [];
        
        /**
         * Each validation rule array.
         */
        foreach ( $validate as $v )
        {
            /**
             * Make sure validate array contains mandatory elements name.
             */
            if ( $this->keyNoExists( ['var', 'rule'], $v ))
            {
                $this->renderViewError( 'Error with mandatory elements in validate array.' );
            }

            /**
             * Hold validation value to be check.
             * Validation rule to apply to the value.
             * URL declaration and know if render or redirect.
             * Failing error message if validation fails.
             * Get HTTP request method.
             */
            $hold = false;
            $rule = $v['rule'];
            $url  = isset( $v['url']  ) ? $v['url']  : false;
            $msg  = isset( $v['msg']  ) ? $v['msg']  : false;
            $http = isset( $v['http'] ) ? $v['http'] : false;
            $type = $this->validate->serverString( 'REQUEST_METHOD' );
            
            /**
             * POST variable validation.
             */
            if ( $type == 'POST' && isset( $_POST[ $v['var'] ] ))
            {
                /**
                 * Set custom validation to false.
                 * HTTP request type must be POST.
                 * Prefix validation method with post.
                 * Hold validation value to compare.
                 */
                $cstm = false;
                $mthd = 'POST' != $type;
                $rule = 'post' . ucwords( $rule );
                $hold = $this->validate->$rule ( $v['var'] );
                
                /**
                 * Add custom validation if was sent.
                 */
                if ( isset( $v['cstm'] ) && $hold )
                {
                   $cstm = eval( 'return ' . $hold . $v['cstm'] . ';' );
                }
                
                /**
                 * Validate values with default rules.
                 */
                $valdt = ! $hold || $cstm || $mthd;
                
                /**
                 * Set validation variable Optional (OPT). If user does not sent any 
                 * value in this variable is OK to pass validation if value is empty.
                 */
                if ( isset( $v['opt'] )  && $v['opt'] )  
                {
                    $valdt = ( !empty( $_POST[ $v['var'] ] ) && !$hold ) || $cstm || $mthd;
                }
                
                /**
                 * Add to return array numeric and name key and value.
                 */
                $return[ $rKey ]     = $hold;
                $return[ $v['var'] ] = $hold;
            }

            /**
             * Variable value validation.
             */
            else if ( $type == 'GET' || $type == 'DELETE' || $type == 'PUT' || $type == 'PATCH' )
            {
                /**
                 * Set custom validation to false.
                 * HTTP request type must be POST.
                 * Hold validation value to compare.
                 */
                $cstm = false;
                $mthd = 'GET' != $type;
                $hold = $this->validate->$rule ( $v['var'] );
                
                /**
                 * Add custom validation if was sent.
                 */
                if ( isset( $v['cstm'] ) && $hold )
                {
                   $cstm = eval( 'return ' . $hold . $v['cstm'] . ';' );
                }
                
                /**
                 * For specific HTTP request type.
                 */
                if( isset( $v['type'] ) )
                {
                    $mthd = strtoupper( $v['type'] ) != $type;
                }
                
                /**
                 * Validate values with default rules.
                 */
                $valdt = ! $hold || $cstm || $mthd;
                
                /**
                 * Set validation variable Optional (OPT). If user does not sent any 
                 * value in this variable is OK to pass validation if value is empty.
                 */
                if ( isset( $v['opt'] )  && $v['opt'] )  
                {
                    $valdt = ( !empty( $v['var'] ) && !$hold ) || $cstm || $mthd;
                }

                /**
                 * Add to return array numeric key and value.
                 * If user set name to return add to the return array.
                 */
                $return[ $rKey ] = $hold;
                if ( isset( $v['name'] )) { $return[ $v['name'] ] = $hold; }
            }
            
            /**
             * Default, fail validation.
             */
            else
            {
                $valdt = true;
            }
            
            /**
             * Redirect if validation fail.
             */
            if ( $url )
            {
                $this->redirectRule( $valdt, $url, $msg );
            }
                
            /**
             * HTTP error code validation.
             */
            else if ( $http )
            {
                $this->httpResponseRule( $valdt, $http, $msg );
            }

            /**
             * Render View if validation fail.
             */
            else
            {
                $this->renderViewRule( $valdt, $msg );
            }

            /**
             * Increment return key.
             */
            $rKey ++;
        }

        return $return;
    }

    /**
     * Key no exists
     *
     * Return true if a specific key does not exists in an array.
     * 
     * @param mixed $key Array or string with key name to check.
     * @param array $array to check if key exists.
     * @return bool.
     */
    private function keyNoExists($key, array $array) : bool
    {
        /**
         * Array key validation.
         */
        if ( is_array( $key ))
        {
            foreach ( $key as $k )
            {
                if ( ! array_key_exists( $k, $array )) { return true; }
            }
        }

        /**
         * String key validation.
         */
        else
        {
            if ( ! array_key_exists( $key, $array )) { return true; }
        }
        
        return false;
    }
    
    /**
     * Get URL data using CURL.
     * 
     * @param string $url URL.
     * @return string.
     */
    protected function getUrlData(string $url) : string
    {   
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        
        $data = curl_exec( $ch );
        
        curl_close( $ch );
        
        return $data;
    }
}