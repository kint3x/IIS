<?php

Class Database {

	public $handle;
	public $error;

	public function __construct(){
		$mysqli = new mysqli("aurelius.aurelserver.eu","iis","iis","iis");

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