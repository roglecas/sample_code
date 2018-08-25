<?php namespace DevApp\Modules\Index\Models;

/*
 * ----------------------------------------------------
 * Dev Project | Rolando Gonzalez
 *    ---------------------------
 *         IndexModel.php
 * ----------------------------------------------------
 */

use DevApp\Application\Model;

class IndexModel extends Model
{
    /**
     * Check for access login.
     * 
     * @param string $userN Username.
     * @param string $passW Password.
     * @return array with Y or N flag.
     */
    public function getLoginUser(string $userN, string $passW = false) : array
    {   
        $query = "
            SELECT  
            FU.user_id    USERID,      PP.first_name FNAME,
            FU.user_name  UNAME,       PP.last_name  LNAME,
            
            ( SELECT XXDEV.XXDEV_FND_WEB_SEC(:userN, :passW) FROM DUAL )  LGN
            
            FROM 
            FND.FND_USER                 FU
            INNER JOIN FND.PER_PEOPLE_F  PP  ON  FU.person_party_id = PP.party_id
            
            WHERE FU.end_date IS NULL AND FU.user_name = :userN
        ";
        
        return $this->oracleFetch( $query, [['name' => ':userN', 'value' => $userN], ['name' => ':passW', 'value' => $passW] ]);
    }
    
    /**
     * Set user logs.
     * 
     * Insert logging user logs.
     * 
     * @param int $userId User Id.
     * @return void.
     */
    public function setUserLog( int $userId) : array 
    {
        $query = "INSERT INTO XXDEV.XXDEV_APP_USER_LOGS (user_id, session_date) VALUES (:userId, SYSDATE)";
        
        $this->oracleQuery($query, [['name' => ':userId', 'value' => $userId]]);
    }
}