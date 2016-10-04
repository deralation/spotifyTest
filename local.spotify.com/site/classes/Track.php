<?php  

class Track extends Thing{

	public function __construct(){
		$this->name = "track";
		$this->table = "Track";
		$this->prefix = "Track";
		$this->fields = array(
			"id" => array(
				"name" 		=> "id",
				"type" 		=> "int",
				"field"  	=> "id",
				"required"	=> true
			),
			"artistName" => array(
				"name" 		=> "artistName",
				"type" 		=> "string",
				"field"  	=> "artistName",
				"required"	=> false
			),
			"trackName" => array(
				"name" 		=> "trackName",
				"type" 		=> "string",
				"field"  	=> "trackName",
				"required"	=> true
			),
			"playlistID" => array(
				"name"		=> "playlistID",
				"type"		=> "int",
				"field"		=> "playlistID",
				"required" 	=> false,
				"relation"	=> array(
					"class"		=> "Playlist",
					"field"		=> "id"
				)
			),
		);
		$this->relations = array(
			"playlistID" => array(
				"class"		=> "Playlist",
				"table"		=> "Playlist",
				"field"		=> "id"
			),
		);

		global $database;
		$database->setCaching(true);	
	}

	public function getOne($param=null) {
		return $this->arrange(parent::getOne($param));
	}

	public function get($limit=null){
		return $this->arrange(PARENT::get($limit));
	}

	public function add($data){
		try {
			var_dump($data);
			$this->create($data);
			return $this->persist();
		} catch(Exception $e) {
			$this->errorMesssage= $e->getMessage();
			return false;
		}
	}
}

?>