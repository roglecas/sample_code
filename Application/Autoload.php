<?php namespace DevApp\Application;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *         ------------
 *         Autoload.php
 * ----------------------------------------------------
 */

class Autoloader
{
    public static function autoloadClass( string $class) : void
    {   
        /**
         * Create an array with all name space class in each element.
         */
        $class = explode( '\\', $class );
        
        /**
         * Remove the first element of the array to discard DevApp\ project's name.
         */
        array_shift( $class );
        
        /**
         * Create a dynamic path to include the class need it.
         */
        $class = $class ? ROOT . implode( DS, $class ) . '.php' : false;
        
        if ( file_exists( $class )) 
        {
            include_once $class;
        }
    }
}

spl_autoload_extensions( '.php' );
spl_autoload_register( __NAMESPACE__ . '\Autoloader::autoloadClass' );
