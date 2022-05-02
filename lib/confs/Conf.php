<?php
class Conf {

	var $smtphost;
	var $dbhost;
	var $dbport;
	var $dbname;
	var $dbuser;
	var $version;
        var $color_arr;
        var $DEFAULT_OT;

        /**        
            By:kartik gondalia
            Date:23-03-2013
            Purpose:Add below fields for working hours and jot category
            Keep Original line here if:-
        */
        var $emp_work_station;
        var $emp_work_hours;
/*===============================================*/
        
	function Conf() {

		$this->dbhost	= 'localhost';
		$this->dbport 	= '3306';
		if(defined('ENVIRNOMENT') && ENVIRNOMENT == 'test'){
		$this->dbname    = 'hrm_fcs';		
		}else {
		$this->dbname    = 'hrm_fcs';
		}
		$this->dbuser    = 'fdbuser';
		$this->dbpass	= 'FiV35pas5';
		$this->version = '2.7.1';

                
                //                jagruti
//                $this->emp_work_station="16,17,20,21";
//                $this->emp_work_hours="8.50";  
                $this->timeformat=":";
                $this->color_arr=array("#BBFFA8","#D2FEFF","#FFE5E1","#F4FFCE","#D3DBFF","#CCC66C");
                $this->DEFAULT_OT=2;
                
//                kartik
               $this->emp_work_station="28,29";
               $this->emp_work_hours="8.5";               
//                kartik               
                
                
		$this->emailConfiguration = dirname(__FILE__).'/mailConf.php';
		$this->errorLog =  realpath(dirname(__FILE__).'/../logs/').'/';
	}
}
?>
