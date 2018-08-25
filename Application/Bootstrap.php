<?php namespace DevApp\Application;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *     -----------------
 *      Bootstrap.php
 * ----------------------------------------------------
 */

use DevApp\Application\Error;
use DevApp\Application\Request;

class Bootstrap
{
    /**
     * Run.
     *
     * @param Request $request Request object.
     * @return void.
     */
    public static function run(Request $request) : void
    {
        /**
         * Set default values.
         */
        $module 	= $request->getModule();
        $method 	= $request->getMethod();
        $arguments 	= $request->getArguments();
        $controllerName = $request->getController();
        $controller     = APP_NMS . DS_NMS . MODULE_NMS . DS_NMS . ucfirst( $module ) . DS_NMS . CONTROLLER_NMS . DS_NMS . $controllerName . CNTRL_NM;

        /**
         * Render error view with error details when controller class does not exits.
         */
        if( ! class_exists($controller) )
        {
            Error::renderErrorHandleView([
                'admin' => 'Class Name: ' . $controller,
                'user'  => 'Bootstrap error code: 12. Controller class not found. Please contact IT department.'
            ]);
        }

        /**
         * Instantiate controller.
         */
        $controller = new $controller;

        /**
         * Render error view with error details when controller class does not exits.
         */
        if( ! is_callable([ $controller, $method ]))
        {
            Error::renderErrorHandleView([
                'admin' => 'Method Name: <strong>' . $method . '</strong> in ' . $controllerName . 'Controller Class.',
                'user'  => 'Bootstrap error code: 14. Method not found. Please contact IT department.'
            ]);
        }

        /**
         * Call method in a particular controller and pass arguments as an array.
         */
        call_user_func_array([ $controller, $method ], $arguments );
    }
}