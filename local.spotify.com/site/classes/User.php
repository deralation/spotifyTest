<?php

/**
 * Class: User
 * @package YOYO
 * @author fiatux <fiatux@fiatux.com>
 */
class User extends Thing {
	
	public function __construct() {
		$this->name = "user";
		$this->table = "User";
		$this->prefix = "User";
		$this->fields = array(
			"id" => array(
				"name" 		=> "id",
				"type" 		=> "int",
				"field"  	=> "id",
				"required"	=> true
			),
			"accessToken" => array(
				"name" 		=> "accessToken",
				"type"		=> "alphanumeric",
				"field" 	=> "accessToken",
				"required"	=> true
			),
			"displayName" => array(
				"name" 		=> "displayName",
				"type"		=> "string",
				"field" 	=> "displayName",
				"required"	=> false
			),
			"spotifyID" => array(
				"name"		=> "spotifyID",
				"type"		=> "int",
				"field"		=> "spotifyID",
				"required"	=> true	
			),
			"creationDate" => array(
				"name"		=> "creationDate",
				"type"		=> "datetime",
				"field"		=> "createDate",
				"required"  => false
			),
		);
		global $database;
		$database->setCaching(true);
	}

	public function add($data) {
		try {
			$this->create($data);
			return $this->persist();
		} catch(Exception $e) {
			$this->errorMessage = $e->getMessage();
			return false;
		}
	}

	public function get($param=null) {
		return $this->arrange(parent::get($param));
		
	}

	public function getOne($param=null) {
		return $this->arrange(parent::getOne($param));
	}

}

?>