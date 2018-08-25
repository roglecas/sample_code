<?php namespace DevApp\Application;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *      ---------------
 *         Model.php
 * ----------------------------------------------------
 */

use PHPMailer;
use DevApp\Application\Error;
use DevApp\Application\Database;

class Model
{
    /** 
     * @var Database
     */
    private $odb;
    private $stid;
    private $oraConn;

    /**
     * Constructor.
     *
     * Initialize database object and set variables.
     *
     * @return void.
     */
    public function __construct()
    {
        $this->stid    = false;
        $this->oraConn = false;
        $this->odb     = new Database();
    }

    /**
     * Destruct Method.
     *
     * When class finishes, commit the database,
     * query transactions and close the connection.
     *
     * @return void.
     */
    public function __destruct()
    {
        if ( $this->oraConn )
        {
            oci_commit( $this->oraConn );
            $this->closeOracleConnection( $this->oraConn );
        }
    }

   /* =====================================================================================================================================
    * =====================================================================================================================================
    *
    *             ORACLE CONNECTION AND QUERY HELPERS
    *
    * =====================================================================================================================================
    * =====================================================================================================================================
    */

    /**
     * Create Oracle Database Connection.
     *
     * Set oraConn variable with a database resource.
     * May use Production instance.
     * This variable was previously configured in the Database class.
     *
     * Database::oracleConnect().
     *
     * @return void.
     */
    public function openOracleConnection() : void
    {
        $this->oraConn = $this->odb->oracleConnect();
    }

    /**
     * Close Oracle Database Connection.
     *
     * @param resource $conn Database Connection.
     * @return void.
     */
    public function closeOracleConnection($conn) : void
    {
        oci_close( $conn );
    }

    /**
     * Oracle Query Execute.
     *
     * Execute Oracle query. Get a query string value and optional
     * parameters. Parameters variable is used if you want to pass 
     * an argument to create a parameterized query. Must be an array 
     * with two element 'name' with the name of the parameter that 
     * is used in the query where clause and 'value' with the value 
     * of the parameter.
     *
     * @param string $query    Query to be executed.
     * @param array  $params   Parameters for the query.
     * @param array  $callInfo Calling Method Name.
     * @return void.
     */
    private function oracleQueryExec(string $query, $params) : void
    {
        @$this->openOracleConnection();

        /**
         * Render error view if oracle connection fails.
         */
        if ( ! $this->oraConn )
        {
            $e = oci_error();

            Error::renderErrorHandleView([
                'admin' => $e['message'],
                'user'  => 'Model error code: 10. Database connection fail. Please contact IT department.'
            ]);
        }

        @$this->stid = oci_parse( $this->oraConn, $query );

        if ( is_array( $params ) && ! empty( $params ))
        {
            /**
             *  Add & (ampersand symbol) in front of parameter $p to pass as a reference for the follow reason:
             *
             *  OCI8 needs the zval of the bound variable to be available when OCIExecute is called.
             *  Pass by reference, or don't bind in a function.
             *  This is similar to example #3 in http://php.net/manual/en/function.oci-bind-by-name.php
             *
             *  "Reason for this new behavior in PHP 7 is that, unlike in PHP 5.6, zval (_zend_value) in PHP 7.0
             *  is no more have reference count field. Only its values field (zend_value) has reference count field.
             *
             *  Since we store the placeholder (i.e., zval variable) [not its content (zend_value)] in php_oci_bind
             *  structure, it gets overwritten when oci_bind_by_name() is called with same variable and different content in it."
             */
            foreach ( $params as &$p )
            {
                /**
                 * Render error view for missing query parameter name.
                 */
                if ( ! array_key_exists( 'name', $p ) || ! array_key_exists( 'value', $p ))
                {
                    Error::renderErrorHandleView([
                        'admin' => 'Missing in parameter array either array element: name or value.',
                        'user'  => 'Model error code: 11. Query parameter error. Please contact IT department.'
                    ]);
                }

                @$bind = oci_bind_by_name( $this->stid, $p['name'], $p['value'] );

                /**
                 * Render error view for query parameter errors.
                 */
                if ( $bind != 1 )
                {
                    Error::renderErrorHandleView([
                        'admin' => 'Parameter name: <b>' . $p['name'] . '</b> with value of: <b>' . $p['value'] . '</b>',
                        'user'  => 'Model error code: 12. Query parameter error. Please contact IT department.'
                    ]);
                }
            }
        }

        /**
         * Render error view for query errors execution.
         */
        if ( ! $this->stid )
        {
            $e = oci_error( $this->oraConn );

            Error::renderErrorHandleView([
                'admin' => $e['message'],
                'user'  => 'Model error code: 13. Error when database query was execute. Please contact IT department.'
            ]);
        }

        @$execQuery = oci_execute( $this->stid, OCI_NO_AUTO_COMMIT );

        /**
         * Render error view for query errors execution.
         */
        if ( ! $execQuery )
        {
            $e = oci_error( $this->stid );

            Error::renderErrorHandleView([
                'admin' => $e['message'] . '<br /><br /><pre>' . $e['sqltext'] . '</pre>',
                'user'  => 'Model error code: 14. Error when database query was execute. Please contact IT department.'
            ]);
        }
    }

    /**
     * Oracle Fetch All.
     *
     * Returns all rows from execute Query. Params variable is used if you want to pass 
     * parameters to create a parameterized query. Must be an array with two elements: 
     * 'name'  with the name of the parameter that is used in the query where clause.
     * 'value' with the value of the parameter.
     *
     * @param string $query  Query to send to DB.
     * @param array  $params Parameters for the where clause.
     * @return array with all rows result.
     */
    public function oracleFetchAll(string $query, $params = false) : array
    {
        $result = [];
        $this->oracleQueryExec( $query, $params );

        while ( $row = oci_fetch_assoc( $this->stid ))
        {
            $result[] = $row;
        }

        oci_free_statement( $this->stid );
        
        return $result;
    }

    /**
     * Oracle Fetch.
     *
     * Returns a single row from execute Query. Params variable is used if you want to pass 
     * parameters to create a parameterized query. Must be an array with two elements:
     * 'name'  with the name of the parameter that is used in the query where clause.
     * 'value' with the value of the parameter.
     *
     * @param string $query  Query to send to DB.
     * @param array  $params Parameters for the where clause.
     * @return array with single row result.
     */
    public function oracleFetch(string $query, $params = false) : array
    {
        $this->oracleQueryExec( $query, $params );
        
        $result = oci_fetch_assoc( $this->stid );

        oci_free_statement( $this->stid );
        
        /**
         * Function oci_fetch_assoc return false if no rows found.
         * To return always an array check if is false then return 
         * an empty array otherwise the rows that query found.
         */
        return $result != false ? $result : [];
    }

    /**
     * Execute Oracle Production Query.
     *
     * Execute query Oracle Production Database.
     * Params variable is used if you want to pass parameters to create a
     * parameterized query. Must be an array with two elements:
     * 'name' with the name of the parameter that is used in the query where clause.
     h* 'value' with the value of the parameter.
     *
     * @param string $query  Query to send to DB.
     * @param array  $params Parameters for the where clause.
     * @return void.
     */
    public function oracleQuery(string $query, $params = false) : void
    {
        $this->oracleQueryExec($query, $params);

        oci_free_statement($this->stid);
    }

    /**
     * Insert Multiple Rows.
     *
     * Take a predefine array with insert statement
     * and do only one insert for multiples rows.
     *
     * @param array $addRows Array with multiples insert statement.
     * @retunr void.
     */
    public function insertMultipleRows(array $addRows) : void
    {
        if ( empty( array_filter( $addRows ))) 
        { 
            return; 
        }
        
        $this->oracleQuery( "INSERT ALL " . implode(' ', $addRows) . " SELECT * FROM DUAL" );
    }

    /*===========================================================================================================================================
    * ===========================================================================================================================================
    *
    *             APP MENU METHODS
    *
    * ===========================================================================================================================================
    * ===========================================================================================================================================
    */

    /**
     * Get Application Modules.
     *
     * You can limit the quantity of module that you want to return.
     * set the limit parameter as a string value.
     *
     * Example '0 AND 5' or '5 AND 10'.
     *
     * @param  string $sort  Column name to sort.
     * @param  string $limit Quantity of module to return.
     * @return array  with modules.
     */
    public function getAppMenuModules($sort, $limit = false) : array
    {
        $condition = $limit == true ? ' WHERE r BETWEEN ' . $limit : '';

        $query  = "
            SELECT *
            
            FROM
            (
                SELECT 
                module_id, module_name, module_icon, module_desc, module_prefix, module_order, menu_order, module_url, ROWNUM R
                
                FROM XXDEV.XXDEV_APP_MODULES
                
                ORDER BY  " . $sort . "
            )
            
            " . $condition;

        return $this->oracleFetchAll( $query );
    }

    /**
     * Get all Sub module by module id.
     *
     * @param int $modId Module id.
     * @return array.
     */
    public function getAppMenuSubModuleByModule($modId) : array
    {
        $query = "SELECT * FROM XXDEV.XXDEV_APP_MODULES_PERM WHERE module_id = :modId ORDER BY app_order";

        return $this->oracleFetchAll($query, [['name' => ':modId', 'value' => $modId]]);
    }

    /**
     * Get Application Menu by Module Name.
     *
     * Usually use on the index controller to load all of the menus
     * to which the user has access on that module.
     *
     * @param string $module Module Name.
     * @return array.
     */
    public function getMenuByModuleName($module) : array
    {
        $query = "
            SELECT MD.module_prefix, MD.module_url, MN.*
            
            FROM
            XXDEV.XXDEV_APP_MODULES                 MD
            INNER JOIN XXDEV.XXDEV_APP_MODULES_MENU MN ON MD.module_id = MN.module_id
            
            WHERE REPLACE(MD.module_name, ' ', '')  = :module
        ";

        return $this->oracleFetchAll($query, [['name' => ':module', 'value' => $module]]);
    }

    /**
     * Get Application Menu By Module Id.
     *
     * @param int $modId Module Id.
     * @return array with menus.
     */
    public function getAppMenuByModuleId($modId) : array
    {
        $query  = "SELECT * FROM XXDEV.XXDEV_APP_MODULES_MENU WHERE module_id = :modId ORDER BY menu_order";

        return $this->oracleFetchAll($query, [['name' => ':modId', 'value' => $modId]]);
    }

    /*===========================================================================================================================================
    * ===========================================================================================================================================
    *
    *             EMAIL HELPERS
    *
    * ===========================================================================================================================================
    * ===========================================================================================================================================
    */

    /**
     * Send Email.
     *
     * @param  stting       $subject    Email Subject.
     * @param  string       $message    Message to send.
     * @param  array|string $emailTo    Email address to send. Also an array with two element: address and name to send.
     * @param  string       $emailFrom  Optional, email from.
     * @param  string       $attachment Optional, attachment path.
     * @return void.
     */
    public function sendEmail(string $subject, string $message, string $emailTo, $emailFrom = false, $attachment = false) : void
    {
        $frmName    = 'Dev App';
        $frmAddress = 'do.not.reply@localhost';

        /**
         * Render error view for missing email settings.
         */
        if ( ! $subject || ! $message || ! $emailTo)
        {
            Error::renderErrorHandleView([
                'admin' => 'Subject, message and emailTo settings must be set.',
                'user'  => 'Model error code: 15. Email settings error. Please contact IT department.'
            ]);
        }

        if ( is_array( $emailFrom ))
        {
            ! array_key_exists( 'address', $emailFrom ) ? $this->printMsg( 'Need to set address element into emailFrom array.' ) : '';

            $frmAddress = $emailFrom['address'];
            $frmName    = isset( $emailFrom['name'] ) ? $emailFrom['name'] : $emailFrom['address'];
        }

        /** 
         * @var PHPMailer 
         */
        $mail = new PHPMailer;

        if ( is_array( $emailTo ))
        {
            if ( count( $emailTo ) == count( $emailTo, COUNT_RECURSIVE ))
            {
                foreach( $emailTo as $e ) 
                {
                    $mail->addAddress($e); 
                }
            }
            else
            {
                foreach( $emailTo as $e )
                {
                    /**
                     * Render error view if address array element does not exist in emialTo array.
                     */
                    if ( ! array_key_exists( 'address', $e ))
                    {
                        Error::renderErrorHandleView([
                            'admin' => 'Set address element in emailTo array with a valid address to send.',
                            'user'  => 'Model error code: 16. Email address error. Please contact IT department.'
                        ]);
                    }

                    $mail->addAddress( $e['address'], isset( $e['name'] ) ? $e['name'] : $e['address'] );
                }
            }
        }
        else
        {
            $mail->addAddress( $emailTo );
        }

        $mail->setFrom( $frmAddress, $frmName );
        
        $attachment ? $mail->addAttachment( $attachment ) : '';
        
        $mail->isHTML( true );
        
        $mail->Subject = $subject;
        $mail->Body    = $message;
        
        $mail->send();

        unset( $mail );
    }

    /*===========================================================================================================================================
    * ===========================================================================================================================================
    *
    *             APP NOTIFICATION USERS MESSAGES
    *
    * ===========================================================================================================================================
    * ===========================================================================================================================================
    */

    /**
     * Get Application notification by user.
     *
     * Check if login user has any notification.
     *
     * @param int $userId User id.
     * @return array.
     */
    public function getAppNotificationByUser($userId) : array
    {
        $query = "
            SELECT 
            msg_subject MSG, MSG_LINK, TO_CHAR(creation_date, 'DD-MON-YYYY') FDATE 
            
            FROM XXDEV.XXDEV_APP_NOTIFICATION 
            
            WHERE to_user_id = :userId
        ";

        return $this->oracleFetchAll($query, [['name' => ':userId', 'value' => $userId]]);
    }
    
    /**
     * Remove Application notification.
     *
     * @param string $msgId  Message Id.
     * @param int    $userId User id.
     * @return void.
     */
    public function removeAppNotification($msgId, $userId) : void
    {
        $query = "DELETE FROM XXDEV.XXDEV_APP_NOTIFICATION WHERE msg_id = :msgId AND to_user_id = :userId";

        $this->oracleQuery($query, [['name' => ':msgId', 'value' => $msgId], ['name' => ':userId', 'value' => $userId]]);
    }
}
