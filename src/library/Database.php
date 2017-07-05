<?php

namespace App\library;

class Database 
{
	private $conn;
	private static $_instance;        
	
	public static function getInstance() 
        {
            
            if(!self::$_instance) { // If no instance then make one
                self::$_instance = new self();
            }
            return self::$_instance;
	}

	private function __construct() 
        {
            $this->conn = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);

            if(mysqli_connect_error()) {
                trigger_error("Failed to conencto to MySQL: " . mysqli_connect_error(), E_USER_ERROR);
            }
	}
	
	private function __clone() { }

	public function getConnection() {
            return $this->conn;
	}
}