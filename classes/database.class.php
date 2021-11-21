<?php

define ("DB_HOST","aurelius.aurelserver.eu");
define("DB_NAME","test_install");
define("DB_USER","test_user");
define("DB_PASS","testpw");	

Class Database {

	public $handle;
	public $error;

	public function __construct(){
		$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

		// Check connection
		if ($mysqli -> connect_errno) {
		  //echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		  $this->error = true;
		}
		$this->handle = $mysqli;
		$this->error = false;
	}

	public function status(){
		echo $this->handle->stat();
	}

	public function close(){
		$this->handle->close();
	}
}