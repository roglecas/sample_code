<?php namespace DevApp\Application;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *       ---------------
 *        Database.php
 * ----------------------------------------------------
 */

class Database
{
    /**
     * Oracle Connection.
     * 
     * Connection to Oracle Database Server.
     * All configurations reside in Application configuration file.
     * 
     * @return resource.
     */
    public function oracleConnect()
    {
        return oci_pconnect( ORA_DEV_USER, ORA_DEV_PASS, ORA_DEV_SERVER . ORA_DEV_PORT . ORA_DEV_INST, ORA_DEFT_CHAR );
    }
}