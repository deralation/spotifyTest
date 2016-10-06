<?php  
class Playlist extends Thing {
	public function __construct(){
		$this->name = "playlist";
		$this->table = "Playlist";
		$this->prefix = "Playlist";
		$this->fields = array(
			"id" => array(
				"name" 		=> "id",
				"type" 		=> "int",
				"field"  	=> "id",
				"required"	=> true
			),
			"name" => array(
				"name" 		=> "name",
				"type" 		=> "string",
				"field"  	=> "name",
				"required"	=> true
			),

			"playlistID" => array(
				"name" 		=> "playlistID",
				"type" 		=> "string",
				"field"  	=> "playlistID",
				"required"	=> false
			),
			"userID" => array(
				"name"		=> "userID",
				"type"		=> "int",
				"field"		=> "userID",
				"required" 	=> true,
				"relation"	=> array(
					"class"		=> "User",
					"field"		=> "id"
				)
			),
		);
		$this->relations = array(
			"userID" => array(
				"class"		=> "User",
				"table"		=> "User",
				"field"		=> "id"
			),
		);

		global $database;
		$database->setCaching(true);	
	}

	public function getOne($param=null) {
		return $this->arrange(parent::getOne($param));
	}

	public function get($param=null) {
		return $this->arrange(parent::get($param));
		
	}

	public function add($data){
		try {
			$this->create($data);
			return $this->persist();
		} catch(Exception $e) {
			$this->errorMessage = $e->getMessage();
			return false;
		}
	}
}



?>