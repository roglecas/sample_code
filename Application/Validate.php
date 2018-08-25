<?php namespace DevApp\Application;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *      ---------------
 *        Validate.php
 * ----------------------------------------------------
 */

use DateTime;

class Validate
{ 
    /**
     * Sanitize string Post variable.
     * 
     * @param string $name Post element Name.
     * @return mixed.
     */
    public static function postString(string $name)
    {
        return filter_input( INPUT_POST, $name, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );
    }
    
    /**
     * Sanitize string Server variable.
     * 
     * @param string $name Server element Name.
     * @return mixed.
     */
    public static function serverString(string $name)
    {
        return filter_input( INPUT_SERVER, $name, FILTER_SANITIZE_STRING );
    }
    
    /**
     * Sanitize string variable.
     * 
     * @param string $string Post element Name.
     * @return mixed.
     */
    public static function string($string)
    {
       return filter_var( $string, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );
    }
    
    /**
     * Validate Phone Number.
     * 
     * Get a variable an compare with a pattern of phone number
     * if fails return false if not the value of the phone number.
     * 
     * @param string $name Phone to be check.
     * @return string|bool Return false in case of fail otherwise the phone value.
     */
    public static function postPhoneNumber( string $name)
    {
        if ( isset( $_POST[ $name ]) && ! empty( $_POST[ $name ]))
        {
            return $_POST[ $name ] = preg_match( '/^\(\d{3}\) \d{3}-\d{4}$/', $_POST[ $name ]) ? $_POST[ $name ] : false;
        }
    }  
    
    /**
     * Validate 24 hour Format.
     * 
     * Get a variable an compare with a pattern of 24 hours format
     * if fails return false if not the value of the time.
     * 
     * @param string $var Time to be checked.
     * @return string|bool Return false in case of fail otherwise the time value.
     */
    public static function time24($var)
    {
        return preg_match( '/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $var ) ? $var : false;
    }   
    
    /**
     * Validate Integer Value.
     * 
     * Get an value and validate with a pattern only for digits. 
     * If validation fails return false otherwise return the value.
     * 
     * Use pattern match since some scripts like sales reports have some rep number starting
     * with 0 like House Rep - 01. with this pattern make sure that 'int' return true.
     *  
     * @param  int $val Variable to check.
     * @return int|bool False in case integer check fail or integer value otherwise.
     */
    public static function int($val)
    { 
        return preg_match( '/^\d+$/', $val ) ? $val : false;
    }
    
    /**
     * Validate POST Integer Value.
     * 
     * Get the name of the POST variable passed in the parameter and validate its value
     * to make sure it brings a integer.
     *  
     * @param  string $name Name of the POST variable.
     * @return int|bool False in case integer check fail or integer value otherwise.
     */
    public static function postInt(string $name)
    {
        if ( isset( $_POST[ $name ]))
        {
            return preg_match( '/^\d+$/', $_POST[ $name ]) ? $_POST[ $name ] : false;
        }    
    }
    
    /**
     * Validate POST Email.
     * 
     * Get a POST variable and validate if it is a real email. 
     * If validation fails return false otherwise return the POST 
     * variable with the element name and its correspond value.
     * 
     * @param  string $name Name of the POST variable to Filter.
     * @return array|bool.
     */
    public static function postEmail(string $name)
    {
        if ( isset( $_POST[ $name ]))
        {
            return filter_input( INPUT_POST, $name, FILTER_VALIDATE_EMAIL );
        }
    }
    
    /**
     * Validate POST URL.
     * 
     * Get a POST variable and validate if it is a real URL. 
     * If validation fails return false otherwise return the POST 
     * variable with the element name and its correspond value.
     * 
     * @param  string $name Name of the POST variable to Filter.
     * @return array|bool.
     */
    public static function postURL(string $name)
    {        
        return filter_input( INPUT_POST, $name, FILTER_VALIDATE_URL );
    }
    
    /**
     * Check if variable is a text.
     * 
     * @param string $text Variable to check.
     * @return boleean.
     */
    public static function isText($text)
    {
        return preg_match( '/^[a-zA-Z]+$/', $text ) ? $text : false;
    }
    
    /**
     * Validate custom rule.
     * 
     * Get a value and validate with a regular expression 
     * pattern set in a custom variable If validation fails 
     * return false otherwise return the value.
     * 
     * @param  var    $value  Value to be check.
     * @param  string $regexp Regular expression to use as a pattern.
     * @return string|bool false in case of fails or a string.
     */
    public static function customChar($value, string $regexp)
    {
        return preg_match( '/' . $regexp . '/', $value ) ? $value : false;
    }
    
    /**
    * Validate Date.
    *
    * Create a date object from a specific input date format passed as first argument 
    * or set default date format DD-MON-YYYY. Return same format as input date format 
    * if nothing passed as second argument or overwrite this logic passing desired 
    * date format you ant to get.
    *
    * @param string $date    Date to validate.
    * @param string $iFormat Optional, input  date format. Default DD-MON-YYYY
    * @param string $oFormat Optional, output date format. Default same as input date format.
    * @return string if is valid date.
    */
    public static function date(string $date, string $iFormat = 'd-M-Y', $oFormat = false)
    {   
        $d = DateTime::createFromFormat( $iFormat, $date );
        
        if ( $d && strtolower( $d->format( $iFormat )) == strtolower( $date )) 
        {    
            return strtoupper( $d->format( $oFormat ? $oFormat : $iFormat ));
        }
    }
}