<?php 
/* 
 * Class PrintSend - Abstract printing class. 
 * Models the actual sending of data to the printer. 
 *  
 * Author: Mick Sear 
 * eCreate, Aug 2005 
 */ 
  
abstract class PrintSend{ 

    protected $data;     
    protected $debug; 

    abstract protected function printJob($queue); 
     
    abstract protected function interpret(); 
     
    public function __construct() {} 

    public function setData($data){  
        //This can be a filename or some ASCII.  A file check should be made in derived classes. 
        $this->data = $data; 
        $this->debug .= "Data set\n"; 
    } 
     
    //Future functionality (?) Could create filters with this for different languages like PCL or ESC/P 
    public static function printerFactory($type){  
        //Return a new instance of a printer driver of type $type. 
        if (include_once 'Drivers/' . $type . '.php') { 
            return new $type; 
        } else { 
            throw new Exception ('Driver not found: $type'); 
        }         
    } 

    public function getDebug(){ 
    return $this->debug; 
    } 

} 

?> 
