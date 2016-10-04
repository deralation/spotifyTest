<?php
class Config extends Thing {

	public function __construct() {
		$this->name = "config";
		$this->table = "Configs";
		$this->prefix = "Config";
		$this->fields = array(
			"id" => array(
				"name" 		=> "id",
				"type" 		=> "int",
				"field"  	=> "ConfigID",
				"required"	=> true
			),
			"key" => array(
				"name" 		=> "key",
				"type" 		=> "string",
				"field"  	=> "ConfigKey",
				"required"	=> true
			),
			"value" => array(
				"name"		=> "value",
				"type"		=> "string",
				"field"		=> "ConfigValue",
				"required" 	=> true
			)
			
		);
		$this->sortingField = "id";
	}

	public function read($key) {
		$this->addFilter("key",$key);
		$this->setLimit(1);
		$config = $this->arrange(PARENT::get());
		//echo "gelen"; var_dump($config); exit();
		if(isset($config[0])) {
			return $config[0]["value"];
		} else {
			//throw new ExceptionLogger("Config ".$key." is not set.");
			return false;
		}
	}

	public function write($key,$value) {
		global $database;
		$query = "UPDATE ".$this->table;
		$query.= " SET ".$this->fields["value"]["field"]."='".$value."'";
		$query.= " WHERE ".$this->fields["key"]["field"]."='".$this->filter("string",$key)."' LIMIT 1";
		return $database->run($query);
	}

}
?>