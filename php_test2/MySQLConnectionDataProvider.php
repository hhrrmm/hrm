<?php

/*
** Initiates DB connections for other files
*/

Class MySQLConnectionDataProvider {
	public $address;
	public $usr;
	public $pw;
	public $DB;

	public function __construct() {
		$this->address = "192.168.44.200";
		$this->usr     = "cmq";
		$this->pw      = "cmq1";
		$this->DB      = "ConsultingMQ";
	}

}//class

?>