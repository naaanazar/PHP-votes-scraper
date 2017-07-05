<?php

namespace App\library;
use App\library\Database;

class Validate
{
    public $error = [];
    
    public function validation($title, $data, $min_size, $max_size, $method = null) 
    {   
        $error = '';
                
        if ($min_size !== null ) {           
            $this->error[$title][] = $this->minSize($data, $min_size);
        }
        
        if ($max_size !== null ) {           
            $this->error[$title][] = $this->maxSize($data, $max_size);
        }
        
        if ($method !== null ) {           
            $this->error[$title][] = $this->$method($data);
        }
        
        if ($this->error != []) {
            $this->error[$title] = array_filter($this->error[$title]);
        }
        if (empty($this->error[$title])) {  
            unset($this->error[$title]);   
        } else {
            
            foreach ($this->error[$title] as $key => $value) {
                $error .= $value . '<br>';
            }
            
            $this->error[$title] = $error;
        }
        
        $data = $this->string_fix($data);
        
        return $data;      
        
    }
    
    public function maxSize($data, $size) 
    {
        if (strlen($data) > $size){
            return "Field is limited to $size characters";
        }
    }
    
    public function minSize($data, $size) 
    {
        if (strlen($data) < $size){
            return "The field must contain > $size symbols";
        }
    }
    
    public function login($field) 
    {           
        if (preg_match("/[^a-zA-Z0-9_-]/", $field)){
                return "Username can contain a-zA-Z0-9_- ";
        }
    }
    
    public function username($field) 
    {           
        if (preg_match("/[^a-zA-Z0-9_-]/", $field)){
                return "Username can contain a-zA-Z0-9_- ";
        }
        
        $db = Database::getInstance();        
        $conn = $db->getConnection();
        
        $sql= "SELECT username FROM users WHERE username='$field'";
            $result=mysqli_query($conn, $sql);
            
        if   (mysqli_num_rows($result)) {
                 return "Username is exist";
        }	 	
    }
    public function confirm($data) 
    {    
        if ($data !== $_POST['password']) {			
            return "Confirm Password and Password not equal";
        }
    }
    
    public function test_input($data) 
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        
        return $data;
    }
    
    function string_fix($data)
    {
        $db = Database::getInstance();        
        $conn = $db->getConnection();        
        $data = $this->test_input($data);
        
        return mysqli_real_escape_string($conn, $data);
	}   
}