<?php namespace DevApp\Application;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *     ---------------------------
 *         Session.php
 * ----------------------------------------------------
 */

class Session
{
    /**
     * Session start.
     * 
     * @return void.
     */
    public static function init() : void
    {
        session_start();
    }
    
    /**
     * Session destroy.
     * 
     * @param mixed $name Optional, session variable to destroy.
     * @return void.
     */
    public static function destroy($name = false) : void
    {
        if( is_array( $name ))
        {
            for( $i = 0; $i < count( $name ); $i++ )
            {
                if( isset( $_SESSION[ $name[$i] ]))
                {
                    unset( $_SESSION[ $name[$i] ]);
                }
            }
            
            return;
        }
        
        if( isset( $_SESSION[ $name ] ))
        {
            unset( $_SESSION[ $name ] );
            
            return;
        }

        session_destroy();
    }
    
    /**
     * Set session variable.
     * 
     * @param string $key   Variable name.
     * @param mixed  $value Value for variable.
     * @return void.
     */
    public static function setVar($key, $value) : void
    {
        if( !empty( $key ))
        {
            $_SESSION[ $key ] = $value;
        }
            
    }
    
    /**
     * Get session variable value.
     * 
     * @param string $key Name of session variable.
     * @return mixed.
     */
    public static function getVar(string $key)
    {
        if( isset( $_SESSION[ $key ]))
        {
            return $_SESSION[ $key ];
        }   
    }
}