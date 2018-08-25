<?php namespace DevApp\Application;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *      ---------------
 *         Error.php
 * ----------------------------------------------------
 */

use DevApp\Application\View;
use DevApp\Application\Request;
use DevApp\Application\Session;

class Error
{
    /**
     * Get script call information.
     * 
     * @return array with calling Method name.
     */
    private static function getCallingInfo() : array
    {
        $callers  = debug_backtrace();  
        $cFromRw  = explode( '\\', $callers[1]['file'] );
        $callFrom = end( $cFromRw );
        
        return [
            'caller' => $callFrom,
            'method' => $callers[2]['function'],        
            'line'   => isset( $callers[1]['line']  ) ? $callers[1]['line']  : '',
            'class'  => isset( $callers[2]['class'] ) ? $callers[2]['class'] : '',       
            'cLine'  => isset( $callers[2]['line']  ) ? $callers[2]['line']  : '',
        ];  
    }
    
    /**
     * Render Error Handle View.
     * 
     * For any error get an array of error and create view 
     * object to render a view with a friendly message.
     * 
     * @param array $errors Array with errors messages.
     * @return void.
     */
    public static function renderErrorHandleView(array $errors) : void
    {
        $error[]  = ['title' => 'Error Message', 'desc' => $errors['user']];
        $request  = new Request();
        $view     = new View( $request );
        $callInfo = self::getCallingInfo();
        
        if ( DEVAPP_ENV_VAR == DEVAPP_ENV_VAL || Session::getVar( 'devAppAdmin' ))
        {
            $error[] = [ 'title' => 'Error Detail', 'desc' => $errors['admin']    ];
            $error[] = [ 'title' => 'Class',        'desc' => $callInfo['class']  ];
            $error[] = [ 'title' => 'Method',       'desc' => $callInfo['method'] ];
            $error[] = [ 'title' => 'Line',         'desc' => $callInfo['line']   ];
            $error[] = [ 'title' => 'Call From',    'desc' => $callInfo['caller'] ];
            $error[] = [ 'title' => 'Call Line',    'desc' => $callInfo['cLine']  ];
        }
        
        $view->assign( 'title',      'Dev Application Error' );
        $view->assign( 'devAppError', $error );
    
        $view->render( 'App' . DS . 'DisplayError' );
        
        exit;
    }
}