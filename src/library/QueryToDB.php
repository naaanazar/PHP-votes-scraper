<?php

namespace App\library;

use App\library\Database;

class QueryToDb
{
    
    private $conn; 
    
    public function __construct()
    {
        $db = Database::getInstance();
        $this->conn = $db->getConnection();        
    }   
    
    public function queryToDB($sql) 
    {        
        
        $result = $this->conn->query($sql);
        
        if ($result !== false) {
            
            return $result;
            
        } else {
           echo $log_sql =  "error" . $sql . "<br>" . mysqli_error($this->conn);
           // header ("location:error.php");
            exit;
        }        
    }
    
    public function getConnection() {
            return $this->conn;
	}
}