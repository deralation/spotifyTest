<?php
#namespace contentmankit;

#use contentmankit\MySQL as Database;

/**
 * Class: Thing
 * @package contentmankit
 * @author fiatux <fiatux@fiatux.com>
 */
class Thing {

	protected $thing;
	private $proper;
	private $id;

	protected $accessControl=true;
	
	protected $paging;
	protected $limit;
	protected $search;
	protected $sorting;
	protected $order;
	protected $filters;
	protected $leftJoins;
	protected $groupBy;
	protected $selectFields;
	protected $count;
	protected $format;
	
	protected $name;
	protected $fields;
	protected $prefix;
	protected $table;
	protected $requiredFields;
	protected $relations;
	protected $searchable;
	protected $sortingField;

	protected $lastInsertID;

	protected $deleteAction;
	
	protected $errorCode;
	protected $errorMessage;

	protected $caching;
	protected $cacheTime;

	protected $rootURL;
	
	public function __construct() {
		$this->rootURL = ROOTURL;
	}
	
	public function set($id) {
		$this->id = $id;
	}
	
	public function clear() {
		unset($this->selectFields);
		unset($this->sorting);
		unset($this->order);
		unset($this->search);
		unset($this->paging);
		unset($this->limit);
	}

	public function setParam($param,$value,$type=null) {
		// Check Value
		if($value=="") return false; // If not provided skip, don't throw exception for practical reasons

		// Filter Value according to type
		if($type=="int")
			$value = (int)$value;
		else if($type=="string")
			$value = (string)$value;
		else 
			$value = $value;

		// Check for special parameters
		if($param=="sorting") {
			$this->setSorting($value);
		} else if($param=="order") {
			$this->setOrder($value);
		} else {
			$this->$param = $value;
		}

		return true;
	}

	public function setAccessControl($param) {
		$this->accessControl = (bool)$param;
	}

	public function setPaging($param) {
		$this->paging = $this->filter("int",$param);
	}

	public function setLimit($limit) {
		$this->limit = $this->filter("int",$limit);
	}

	public function setSorting($param) {
		if ( preg_match('/\s/',$param) ) {
			$temp = explode(" ",$param);
			if(count($temp)>1) {
				$this->setOrder($temp[1]);
			} 
			$this->sorting = $this->filter("string",$temp[0]);
		} else {
			$this->sorting = $this->filter("string",$param);
		}
	}

	public function setOrder($param) {
		if(in_array(strtolower($param),array("a","asc","ascending","az"))) $this->order = "ASC";
		else $this->order = "DESC";
	}

	public function setCaching($input) {
		if($input>0 && $input!==false)
			$this->cacheTime = $input;
		else
			unset($this->cacheTime);
	}
	
	public function getParam($param) {
		//echo "this->param".$this->$param."zzz";
		if(isset($this->$param)) 
			return $this->$param;
		else
			return null;
	}

	public function getCount() {
		if(isset($this->count)) return $this->count;
		else return false;
	}

	public function getTotalPages() {
		if(isset($this->count)) {
			$limit = 10;
			if(isset($this->limit)) $limit = $this->limit;
			return ceil($this->count/$limit);
		} else {
			throw new ExceptionLogger("Page count could not be calculated since item count is not set.");
		}
	}

	public function getRelationStructure() {
		$relations = null;
		if(isset($this->relations)) {
			if(count($this->relations)>0) {
				$relations = array();
				foreach($this->relations as $key=>$relation) {
					if(isset($relation["class"])) {
						$related = new $relation["class"]();
						$relations[$related->name] = array(
								"class" => $relation["class"],
								"table"	=> $related->table,
								"name"	=> $related->name,
								"fields"=> $related->fields
							);
					}
				}
			}
		} 
		return $relations;
	}

	public function selectFields($param="*") {
		//echo "gelen. ".$param;
		try {
			$selectFields = array();
			if($param!="*" && is_array($param)) {
				foreach($param as $p) {
					$p = trim($p);
					if(strpos($p,".")!==false) {
						$check = explode(".",$p);
						//echo $p;
						if($check[0]==$this->name) {
							$selectFields[] = $this->table.".".$this->filter("string",$p);
						} else {
							$relationStructure = $this->getRelationStructure();
							if(
								isset($relationStructure)
								&& isset($relationStructure[$check[0]]) 
								&& isset($relationStructure[$check[0]]["fields"][$check[1]])
							) {
								$selectFields[] = $relationStructure[$check[0]]["table"].".".$relationStructure[$check[0]]["fields"][$check[1]]["field"];
							}
						}
					} else if(isset($this->fields[$p])) {
						$selectFields[] = $this->table.".".$this->fields[$p]["field"];
					} else {
						// nothing
					}
				}
			} else if($param!="*") {
				$temp = explode(",",$param);
				if(count($temp)>0) {
					foreach($temp as $p) {
						$p = trim($p);
						if(strpos($p,".")!==false) {
							$check = explode(".",trim($p));
							if($check[0]==$this->name) {
								$selectFields[] = $this->table.".".$this->fields[$check[1]]["field"]." AS ".$this->name."_".$check[1];
							} else {
								$relationStructure = $this->getRelationStructure();
								if(
									isset($relationStructure)
									&& isset($relationStructure[$check[0]]) 
									&& isset($relationStructure[$check[0]]["fields"][$check[1]])
								) {
									$selectFields[] = $relationStructure[$check[0]]["table"].".".$relationStructure[$check[0]]["fields"][$check[1]]["field"]." AS ".$relationStructure[$check[0]]["name"]."_".$relationStructure[$check[0]]["fields"][$check[1]]["name"];
								}
							}
						} else if(isset($this->fields[$p])) {
							$selectFields[] = $this->table.".".$this->fields[$p]["field"];
						} else {
							// nothing
						}
					}
				} 
			} else {

			}
			//echo "here"; var_dump($selectFields); exit();
			if(count($selectFields)>0) $this->selectFields = $selectFields;
			return true;
		} catch(Exception $e) {
			return false;
		}
	}

	public function addLeftJoin($foreign,$local=null) {
		try {
			if(!isset($this->leftJoins))
				$this->leftJoins = array();

			if(strpos($foreign,".")===false)
				throw new ExceptionLogger("Invalid parameter");
			$foreignObject = explode(".",$foreign);
			$foreignObjectClassName = $foreignObject[0];
			$foreignObjectAttribute = $foreignObject[1];
			$foreignObject = new $foreignObjectClassName();
			$foreignTable = $foreignObject->table;
			$foreignField = $foreignObject->fields[$foreignObjectAttribute]["field"];

			if($local==null) {
				$localTable = $this->table;
				$localField = $this->fields["id"]["field"];
			} else if(strpos($foreign,".")===false) {
				$localTable = $this->table;
				$localField = $this->fields[$local]["field"];
			} else {
				$localObject = explode(".",$local);
				$localObjectClassName = $localObject[0];
				$localObjectAttribute = $localObject[1];
				$localObject = new $localObjectClassName();
				$localTable = $localObject->table;
				$localField = $localObject->fields[$localObjectAttribute]["field"];
			}

			$this->leftJoins[$foreignTable] = $foreignTable.".".$foreignField."=".$localTable.".".$localField;

		} catch(Exception $e) {
			return false;
		}
	}

	public function addGroupBy($input) {
		
		if(!isset($this->groupBy))
			$this->groupBy = array();

		if(is_array($input)) {
			foreach($input as $i) {
				$this->groupBy[] = $i;
			}
		} else if(is_string($input)) {
			$this->groupBy[] = $input;
		} else {
			throw new Exception("Unknown input");
		}

		return true;
	}
	
	public function addFilter($key=null,$value=null,$operator="=") {

		// Key and value are required, operation is optional where default is =
		if(!isset($key) || !isset($value)) {
			throw new ExceptionLogger("Filter parameters are missing.");
			return false;
		}

		// If this is the first filter let's create an array, otherwise multiple filters will be ANDed in the query
		if(!isset($this->filters)) $this->filters = array();

		// If it is empty string, skipping the request and not adding the filter
		if($value=="") return true;

		// Let's check class type
		$checkClass = strpos($key,".");
		if($checkClass===false) {
			// It is an filter of current class field
			if(isset($this->fields[$key])) {
				$filter = array();
				$filter["key"] = $key;
				$filter["operator"] = $operator;
				$filter["value"] = $value;
				$this->filters[] = $filter;
			} else {
				throw new ExceptionLogger("Invalid key provided for the filter: ".$key);
			}
		} else {
			// It is an filter of another related class field
			$className = substr($key,0,$checkClass);
			$related = new $className();
			
			if(isset($related->fields[substr($key,$checkClass+1)])) {
				$filter = array();
				$filter["key"] = $key;
				$filter["operator"] = $operator;
				$filter["value"] = $value;
				$this->filters[] = $filter;
			} else {
				throw new ExceptionLogger("Invalid key provided for the filter: ".substr($key,$checkClass+1));
			}
		}
		
		
	}
	
	public function getError() {
		if(isset($this->errorMessage)) return $this->errorMessage;
		else return error_get_last();
	}
	
	public function create($data=null) {
		if($data==null) {
			$this->thing = array();
		}else if(is_array($data)){
			if(isset($data["id"]))
				unset($data["id"]);
			$this->thing = $data;
		}else{
			throw new ExceptionLogger('create() parameter must be null or array.');
		}
	}
	
	public function load($data=null) {
		if($data==null) {
			throw new ExceptionLogger('Data is required to load.');
			return false;
		} else if(is_array($data)) {
			$this->thing = $data;
			return true;
		} else {
			throw new ExceptionLogger('Data must be an array to load.');
			return false;
		}
		
	}

	protected function getRequiredFields() {
		if(isset($this->requiredFields)) {
			return $this->requiredFields;
		} else if(isset($this->fields)) {
			$this->requiredFields = array();
			foreach($this->fields as $key=>$field) {
				if(isset($field["required"])) {
					// Don't return id in this array
					if($field["required"]==true && $field["name"]!="id") {
						$this->requiredFields[] = $field["name"];
					}
				} 
			}
			return $this->requiredFields;
		} else {
			throw new Exception ("Fields are not set for the Class.");
		}
	}
	
	public function persist() {
		global $database;
		if(!is_array($this->thing)) {
			throw new ExceptionLogger("There is no data to persist.");
			return false;
		}
		
		// Check whether it's a new row or an update using id/"name" keys
		if(isset($this->thing[$this->name]) || isset($this->thing["id"]) ) {
			// So it's an update

			// Data that's going to be update will be stored in this array
			$update = array();

			// Required conditions to be matched for an update will be stored in this array
			$conditions = array();

			// Let's analyse provided data
			// If key is named as id or named as the same name as the class itself it's automatically considered as an condition
			foreach($this->thing as $k=>$v) {
				if($k==$this->name) {
					$conditions[$this->fields["id"]["field"]] = (int)$v;
				} else if($k=="id") {
					$conditions[$this->fields["id"]["field"]] = (int)$v;
				} else if(array_key_exists($k,$this->fields)) {
					// Check for enum
					if(isset($this->fields[$k]["values"])) {
						// Check and correct for enum if available values are provided for the field in the Class.
						$key = array_search($v,$this->fields[$k]["values"]);
						if($key!==false) {
							$update[$this->fields[$k]["field"]] = $this->filter($this->fields[$k]["type"],$key);
						} else if(array_key_exists($v,$this->fields[$k]["values"])) {
							$update[$this->fields[$k]["field"]] = $this->filter($this->fields[$k]["type"],$v);
						} else {
							throw new ExceptionLogger($k.' value is not valid.');
						}
					} else {
						// Convery Array values to JSON, if required format is json but array value if provided
						if($this->fields[$k]["type"]=="json" && is_array($v))
							$v = json_encode($v);

						// Filter Values
						$update[$this->fields[$k]["field"]] = $this->filter($this->fields[$k]["type"],$v);
					}
				}
			}

			//var_dump($update);

			// Add update date to data, if it's provided in subclass's fields
			if(isset($this->fields["updateDate"])) {
				if($this->fields["updateDate"]["type"]=="datetime")
					$update[$this->fields["updateDate"]["field"]] = date("Y-m-d H:i:s");
				else if($this->fields["updateDate"]["type"]=="timestamp")
					$update[$this->fields["updateDate"]["field"]] = date("U");
				else
					throw new ExceptionLogger('Unknown type for updateDate.');
			}

			// DON'T Populate default values, you may override existing values
			/*foreach($this->fields as $field) {
				if(isset($field["default"]) && !isset($this->thing[$field["name"]])) {
					$this->thing[$field["name"]] = $field["default"];
					$update[$this->fields[$field["name"]]["field"]] = $field["default"];
				}
			}*/

			// DON'T Check for required fields, user may be updating only the necessary ones
			/*$requiredFields = $this->getRequiredFields();
			$availableFields = array_keys($this->thing);
			if(count(array_intersect($availableFields,$requiredFields))!=count($requiredFields))
				throw new ExceptionLogger('Zorunlu alanlardan bazılarını eksik girdiniz. Gerekli olanlar: '.implode(", ",$this->requiredFields).'. Girdikleriniz: '.implode(",",$availableFields));
				*/

			//var_dump($update); exit();
			//if($this->table=="Member") { echo "<pre>"; var_dump($update); exit(); }

			// All good?
			if(count($conditions)>0 && count($update)) {
				return $database->update($this->table, $update, $conditions);
			} else {
				throw new ExceptionLogger('Condition(s) or update data are missing.');
			}
		} else {
			// Add Row
			$new = array();

			// Populate default values
			foreach($this->fields as $key=>$field) {
				if(isset($field["default"]) && $field["required"]==true && !isset($this->thing[$field["name"]])) {
					$this->thing[$key] = $field["default"];
					$new[$this->fields[$field["name"]]["field"]] = $field["default"];
				}
			}

			// Conversions
			foreach($this->thing as $k=>$v) {
				if(array_key_exists($k,$this->fields)) {
					if(isset($this->fields[$k]["values"])) {
						
						// Check and correct for enum if available values are provided for the field in the Class.
						$key = array_search($v,$this->fields[$k]["values"]);
						
						if($key!==false) {
							$new[$this->fields[$k]["field"]] = $this->filter($this->fields[$k]["type"],$key);
						} else if(array_key_exists($v,$this->fields[$k]["values"])) {
							$new[$this->fields[$k]["field"]] = $this->filter($this->fields[$k]["type"],$v);
						} else {
							throw new ExceptionLogger($k.' value is not valid: '.$v);
						}
					} else {
						// Convery Array values to JSON, if required format is json but array value if provided
						if($this->fields[$k]["type"]=="json" && is_array($v))
							$v = json_encode($v);
						// Filter using its type
						$new[$this->fields[$k]["field"]] = $this->filter($this->fields[$k]["type"],$v);	
					}
				} else {
					// Check if all parameters provided make sense
					// Not recommended, disabled by default -- it requires all array keys to fit to the Class
					// throw new ExceptionLogger("A parameter is not recognised for the object: ".$k);
				}
			}

			// Check for required fields				
			$requiredFields = $this->getRequiredFields();
			$availableFields = array_keys($this->thing);
			if(count(array_intersect($availableFields,$requiredFields))!=count($requiredFields)) 
				throw new ExceptionLogger('Zorunlu bazı değerler formda sağlanmadı. Zorunlu: '.implode(", ",$this->requiredFields).'. Sağlanan: '.implode(", ",$availableFields).". Farklar: ".implode(", ",array_diff($requiredFields,$availableFields)));

			// Check if we have any data to insert
			if(count($new)>0) {

				//echo "<pre>"; var_dump($new); 

				// Add creationDate to the data if it is defined in the class field names
				if(isset($this->fields["creationDate"])) {
					if($this->fields["creationDate"]["type"]=="datetime")
						$new[$this->fields["creationDate"]["field"]] = date("Y-m-d H:i:s");
					else if($this->fields["creationDate"]["type"]=="timestamp")
						$new[$this->fields["creationDate"]["field"]] = date("U");
					else
						throw new ExceptionLogger('Unknown data type for creationDate.');
				}			

				// Insert data to the database
				try {
					$result = $database->insert($this->table,$new);
					if($result) {
						$this->lastInsertID = $database->getInsertID();
						return true;
					} else {
						throw new ExceptionLogger($database->getError());
						return false;
					}
				} catch(Exception $e) {
					$this->errorMessage = $e->getMessage();
					return false;
				}
				
			} else {
				throw new ExceptionLogger('Missing data for create operation.');
			}
		}
	}

	public function getInsertID() {
		return $this->lastInsertID;
	}

	public function getByID($condition=null) {
		$result = $this->get($condition);
		$return = array();
		if(count($result)>0) {
			foreach($result as $r) {
				$return[$r["id"]] = $r;
			}
		} 
		return $return;
	}
	
	public function get($condition=null) {
		if(isset($this->table)) {
			

			$limit = $this->limit;

			global $database;
			$query="SELECT ";
			if(isset($this->selectFields)) {
				$query.= implode(",",$this->selectFields);
			} else {
				foreach($this->fields as $property) {
					$query.= $this->table.".".$property["field"].", ";
				}
				$query= rtrim($query,", ");
				if(isset($this->relations)) {
					if(count($this->relations)>0) {
						foreach($this->relations as $key=>$relation) {
							if(isset($relation["class"]) && isset($relation["table"])) {
								$related = new $relation["class"]();
								$relatedFields = $related->fields;
								foreach($relatedFields as $relatedField) {
									$query.= ", ".$related->table.".".$relatedField["field"]." AS ".$related->name."_".$relatedField["name"];;
								}
							} else if(isset($relation["table"])) {
								$query.=", ".$relation["table"].".*";
							} else {
								// Skip relation on queries
							}
						}
					}
				}
			}
			
			$query.=" FROM ".$this->table;
			if(isset($this->relations)) {
				foreach($this->relations as $key=>$relation) {
					if(isset($relation["table"])) {
						$query.=" LEFT JOIN ".$relation["table"]." ON ".$this->table.".".$this->fields[$key]["field"]."=".$relation["table"].".".$relation["field"];
					}
				}
			}
			if(isset($this->leftJoins)) {
				foreach($this->leftJoins as $table=>$condition) {
					$query.=" LEFT JOIN ".$table." ON ".$condition;
				}
			}
			$conditionCount = 0;
			if(is_array($condition)) {
				foreach($condition as $key=>$value) {
					if($conditionCount>0) $query.=" AND"; else $query.=" WHERE"; $conditionCount++;
					$query.=" ".$key."='".$value."'";
				}	
			} else if(is_numeric($condition)) {
				$limit = (int)$condition;
			}

			// Build Where with Filters
			if(isset($this->filters)) {
				foreach($this->filters as $filter) {

					if($filter["value"]=="") continue;

					$checkClass = strpos($filter["key"],".");
					if($checkClass===false) {
						if(isset($this->fields[$filter["key"]]["field"]))
							$databaseField = $this->table.".".$this->fields[$filter["key"]]["field"];
						else
							throw new ExceptionLogger("Unknown filter key provided: ".$filter["key"]);
					} else {
						$className = substr($filter["key"],0,$checkClass);
						$related = new $className();
						if(isset($related->fields[substr($filter["key"],$checkClass+1)]["field"]))
							$databaseField = $related->table.".".$related->fields[substr($filter["key"],$checkClass+1)]["field"];
						else
							throw new ExceptionLogger("Unknown filter key provided: ".substr($filter["key"],$checkClass+1));
					}

					if($conditionCount>0) $query.=" AND"; else $query.=" WHERE"; $conditionCount++;
					
					if(is_array($filter["value"])) {
						$temp = array();
						foreach($filter["value"] as $v) {
							if(isset($this->fields[$filter["key"]]["values"])) {
								// Check and correct for enum if available values are provided for the field in the Class.
								$key = array_search($v,$this->fields[$filter["key"]]["values"]);
								if($key!==false) {
									$temp[] = $this->filter("string",$key);
								} else if(array_key_exists($v,$this->fields[$filter["key"]]["values"])) {
									$temp[] = $v;
								} else {
									throw new ExceptionLogger($filter["key"].' value is not valid: '.$v);
								}
							} else {
								$temp[] = $this->filter("string",$v);
							}
						}
						$query.=" ".$databaseField." IN ('".implode("','",$temp)."')";
						// @todo put enum check here too!!!
					} else if(in_array($filter["operator"],array("=",">",">=","<","<=","!="))) {
						// Check enum
						if(isset($this->fields[$filter["key"]]["values"])) {
							// Check and correct for enum if available values are provided for the field in the Class.
							$key = array_search($filter["value"],$this->fields[$filter["key"]]["values"]);
							if($key!==false) {
								$query.=" ".$databaseField.$filter["operator"]."'".$this->filter("string",$key)."'";
							} else if(array_key_exists($v,$this->fields[$k]["values"])) {
								$query.=" ".$databaseField.$filter["operator"]."'".$this->filter("string",$filter["value"])."'";
							} else {
								throw new ExceptionLogger($filter["key"].' value is not valid.');
							}
						} else {
							$query.=" ".$databaseField.$filter["operator"]."'".$this->filter("string",$filter["value"])."'";
						}
						//$query.=" ".$databaseField.$filter["operator"]."'".$this->filter("string",$filter["value"])."'";
					} else {
						throw new ExceptionLogger("Unknown operator provided for the filter: ".$filter["operator"]);
					}
					
				}
			}

			// Build Where with Search Parameters
			if(isset($this->search)) {
				if($conditionCount>0) $query.=" AND"; else $query.=" WHERE"; $conditionCount++;
				if(isset($this->searchable)) {
					$searchableCounted = 0;
					foreach($this->searchable as $key=>$comparison) {
						if(isset($this->fields[$key])) {
							if($searchableCounted==0) $query.=" (";
							if($searchableCounted>0) $query.=" OR";
							$query.=" ";
							if(strpos($this->fields[$key]["field"],".")===false)
								$query.=$this->table.".";
							$query.=$this->fields[$key]["field"];
							if($comparison=="equal" || $comparison=="=") {
								$query.="='".$this->filter("string",$this->search)."'";
							} else if($comparison=="contains") {
								$query.=" LIKE '%".$this->filter("string",$this->search)."%'";
							} else {
								throw new ExceptionLogger("Unknown search comparison parameter provided in the class constructor: ".$comparison);
							}
							$searchableCounted++;
						} else {
							throw new ExceptionLogger($key." is set as searchable but is not one of class fields.");
						}
					}
					if($searchableCounted>0) $query.=")";
				} else if(isset($this->fields["id"])) {
					$query.=" ".$this->fields["id"]["field"]."=".$this->filter("int",$this->search);
				} else {
					throw new ExceptionLogger("No searchable fields are set for this class.");
				}
			}

			// Build Group By
			if(isset($this->groupBy) && count($this->groupBy)>0) {
				$query.= " GROUP BY";

				foreach($this->groupBy as $counter=>$g) {
					if($counter>0)
						$query.=", ";
					if(strpos($g,".")===false) {
						$query.=" ".$this->fields[$g]["field"];
					} else {
						// @todo add group by for relations
					}
				}
			}

			// Build Count Query
			$countQuery = preg_replace('~SELECT.*?FROM~s', 'SELECT COUNT('.$this->table.'.'.$this->fields["id"]["field"].') AS COUNT FROM', $query);

			// Build Sorting
			if(isset($this->sorting)) {
				if(isset($this->fields[$this->sorting])) {
					$query.=" ORDER BY ".$this->table.".".$this->fields[$this->sorting]["field"];
					if(isset($this->order)) $query.=" ".$this->order;
				} else if(strpos($this->sorting,".")!==false) {
					$check = explode(".",$this->sorting);
					$relatedObject = new $check[0]();
					$query.=" ORDER BY ".$relatedObject->table.".".$relatedObject->fields[$check[1]]["field"];
					if(isset($this->order)) $query.=" ".$this->order;
				} else {
					throw new ExceptionLogger("Unknown field provided for sorting: ".$this->sorting);
				}
			} else if(isset($this->fields["id"])) {
				$query.=" ORDER BY ".$this->table.".".$this->fields["id"]["field"]." DESC";
			}

			// Build Limit
			if(isset($limit)) {
				$paging = 1;
				if(isset($this->paging)) $paging = $this->paging;
				$query.=" LIMIT ".($paging-1)*$limit.",".$limit;
			}

			// DEBUG
			//echo $query; die("ss"); exit();
			//if($this->table=="Stats") { echo $query.PHP_EOL; }
			
			// Perform Query
			try {
				$database->select($query,$this->cacheTime);
				$return = $database->getAll();
				$database->select($countQuery);
				$temp = $database->getOne();
				if($temp["COUNT"]>0) $this->count = $temp["COUNT"]; else $this->count = 0;
				
				return $return;
			} catch(Exception $e) {
				$this->errorMessage = $e->getMessage();
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function getPrimaryField() {
		if(is_array($this->fields)) {
			foreach($this->fields as $f) {
				if($f["name"]=="id") return $f["field"];
			}
		}
	}
	
	public function getOne($condition=null) { 
		if(isset($this->table)) {

			global $database;
			$query="SELECT ";
			if(isset($this->selectFields)) {
				//$query.= $this->table.".".implode(", ".$this->table.".",$this->selectFields);
				$query.= implode(", ",$this->selectFields);
			} else {
				foreach($this->fields as $property) {
					$query.= $this->table.".".$property["field"].", ";
				}
				$query = rtrim($query,", ");
				if(isset($this->relations)) {
					if(count($this->relations)>0) {
						foreach($this->relations as $key=>$relation) {
							if(isset($relation["class"]) && isset($relation["table"])) {
								$related = new $relation["class"]();
								$relatedFields = $related->fields;
								foreach($relatedFields as $relatedField) {
									$query.= ", ".$related->table.".".$relatedField["field"]." AS ".$related->name."_".$relatedField["name"];
								}
							} else if(isset($relation["table"])) {
								$query.=", ".$relation["table"].".*";
							} else {
								// Skip relation on queries
							}
						}
					}
				}
			}
			
			$query.=" FROM ".$this->table;
			if(isset($this->relations)) {
				if(count($this->relations)>0) {
					foreach($this->relations as $key=>$relation) {
						if(isset($relation["table"])) {
							$query.=" LEFT JOIN ".$relation["table"]." ON ".$this->table.".".$this->fields[$key]["field"]."=".$relation["table"].".".$relation["field"];
						}
					}
				}
			}
			$conditionCount = 0;
			if(is_array($condition)) {
				$query.= " WHERE";
				$i=0;
				foreach($condition as $key=>$value) {
					if($i>0) $query.=" AND";
					$query.=" ".$key."='".$value."'";
					$i++;
				}	
			} else if(is_numeric($condition)) {
				$primaryKey = $this->getPrimaryField();
				if($primaryKey!==false) {
					$query.=" WHERE ".$this->table.".".$primaryKey."=".$condition;
				} else {
					throw new ExceptionLogger('Primary key could not be detected.');
				}
			} else if(isset($this->id)) {
				$primaryKey = $this->getPrimaryField();
				if($primaryKey!==false) {
					$query.=" WHERE ".$primaryKey."=".$condition;
				} else {
					throw new ExceptionLogger('Primary key could not be detected.');
				}
			} else {
				throw new ExceptionLogger("No parameter is set for getOne.");
			}

			$query.= " LIMIT 1";
			
			//if($this->table=="Payment") { echo $query; exit(); }

 			$database->select($query,$this->cacheTime);
			$return = $database->getOne();
			if(isset($return)) return $return;
			else return false;
		} else {
			return false;
		}
	}

	public function delete($condition) {
		global $database;

		if(!$this->checkAccess($this->name.".delete")) return $this->noAccess();

		if(is_numeric($condition)) {
			$condition = array($this->fields["id"]["field"]=>$condition);
		} else if(is_array($condition)) {
			$arrange = $condition;
			$condition = array();
			foreach($arrange as $field=>$value) {
				if(isset($this->fields[$field]["field"])) {
					$condition[$this->fields[$field]["field"]] = $this->filter($this->fields[$field]["type"],$value);
				} else {
					throw new ExceptionLogger("Delete condition field does not exist: ".$field);
				}
			}
		} else {
			throw new ExceptionLogger('Delete parameter must be integer for a single condition or array for multiple conditions.');
			return false;
		}

		if(isset($this->deleteAction)) {
			if(isset($this->fields[$this->deleteAction["field"]])) {
				$fieldName = $this->fields[$this->deleteAction["field"]]["field"];
				$fieldValue = $this->deleteAction["flag"];
				return $database->update($this->table,array($fieldName=>$fieldValue),$condition);
			}
		} else {
			return $database->deleteAll($this->table,$condition);
		}
	}

	public function sort($objectIDs) {
		//var_dump($objectIDs);
		if(!isset($this->sortingField))
			throw new ExceptionLogger("Sorting field is not set.");
		if(is_array($objectIDs)) {
			if(isset($objectIDs[0]) && is_numeric($objectIDs[0])) {
				global $database;
				$query = "UPDATE ".$this->table." SET ".$this->sortingField." = CASE ".$this->fields["id"]["field"];
				foreach($objectIDs as $counter=>$ID) {
					$query.=" WHEN ".$ID." THEN ".$counter;
				}
				$query.=" END";
				$result = $database->run($query);

				if($result) {
					try {
						global $activity;
						$activity->log("event.".$this->name.".sort");
					} catch(Exception $e) {
						throw new ExceptionLogger("Cannot log activity.");
					} 
				}

				return $result;
			} else {
				throw new ExceptionLogger("Unknown parameter, not numeric");
			}
		} else {
			throw new ExceptionLogger("Unknown parameter, not an array");
		}
	}
	
	public function ping() {
		echo "pong";
	}
	
	public function arrange($data=null) {
		if($data==null) $data = $this->thing;
		if(!is_array($data)) return null; //throw new ExceptionLogger('No data to arrange.');
		$proper = array();
		
		if(isset($data[0])) {
			foreach($data as $d) {
				$proper[] = $this->arrangeOne($d);
			}
		} else {
			$proper = $this->arrangeOne($data);
		}
		
		$this->proper = $proper;
		
		return $proper;
	}
	
	public function arrangeOne($data) {
		if(!is_array($data)) return null; //throw new ExceptionLogger('No data to arrange.');
		if(isset($data["isArrangedBefore"])) return $data;
		// Direct Data
		//echo "giren <pre>"; var_dump($data);
		$proper = array();
		if(isset($this->fields)) {
			foreach($this->fields as $key=>$properties) {
				//echo "name: ".$properties["name"]." field: ".$properties["field"]." ";
				if(isset($data[$properties["field"]])) {
					$proper[$key] = $data[$properties["field"]];
					// Enum overwrite
					if(isset( $properties["values"][$data[$properties["field"]]] )) {
						$proper[$key] = $properties["values"][$data[$properties["field"]]];
					} else if($properties["type"]=="int") {
						$proper[$key] = (int)$proper[$key];
					} else {

					}
					// JSON Decode overwrite
					if($properties["type"]=="json")
						$proper[$key] = json_decode($data[$properties["field"]],true);
					
					unset($data[$properties["field"]]);
				} else if(isset($data[$this->name."_".$properties["name"]])) {
					$proper[$key] = $data[$this->name."_".$properties["name"]];
					// Enum overwrite
					if(isset( $properties["values"][$data[$this->name."_".$properties["name"]]] )) {
						$proper[$key] = $properties["values"][$data[$this->name."_".$properties["name"]]];
					} else if($properties["type"]=="int") {
						$proper[$key] = (int)$proper[$key];
					} else {

					}
					// JSON Decode overwrite
					if($properties["type"]=="json")
						$proper[$key] = json_decode($data[$this->name."_".$properties["name"]],true);
					// Integer Overwrite
					
					unset($data[$this->name."_".$properties["name"]]);
				} else if(isset($properties["default"])) {
					$proper[$key] = $properties["default"];
				} else {
					$proper[$key] = null;
				}
			}
			
			if(count($data)>0 && isset($this->relations)) {
				
				foreach($this->relations as $r) {
					if(isset($r["class"])) {
						$related = new $r["class"]();
						$relatedFields = $related->fields;
						foreach($relatedFields as $key=>$relatedField) {
							//if($related->name=="reservation") var_dump($data);
							//echo "checking for key ".$relatedField["name"]; echo " object: ".$related->name; var_dump($data);
							if(isset($data[$related->name."_".$relatedField["name"]])) {
								$proper[$related->name][$key] = $data[$related->name."_".$relatedField["name"]];
								/*
								// JSON Decode overwrite
								if($relatedField["type"]=="json")
									$proper[$related->name][$key] = json_decode($data[$related->name."_".$relatedField["name"]],true);
								*/
								unset($data[$related->name."_".$relatedField["name"]]);
								continue;
							}
							if(isset($data[$relatedField["field"]])) {
								$proper[$related->name][$key] = $data[$relatedField["field"]];
								unset($data[$relatedField["field"]]);
								continue;
							}
						}
					}
				}
			}
		}
		$proper["isArrangedBefore"] = true;
		//echo "çıkan "; var_dump($proper);
		return $proper;
		 
	}

	protected function getAlias($text, $options = "dashes") {
		$text = trim($text);
		$search = array('Ç', 'ç', 'Ğ', 'ğ', 'ı', 'İ', 'Ö', 'ö', 'Ş', 'ş', 'Ü', 'ü', ' ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'A', 'A', 'A', 'Ç', 'C', 'C', 'C', 'C', 'D', 'D', 'È', 'É', 'Ê', 'Ë', 'E', 'E', 'E', 'E', 'E', 'G', 'Ğ', 'G', 'G', 'H', 'H', 'Ì', 'Í', 'Î', 'Ï', 'İ', 'Ñ', 'N', 'N', 'N', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'O', 'O', 'O', 'Œ', 'R', 'R', 'R', 'S', 'Ş', 'S', 'Š', 'T', 'T', 'T', 'Ù', 'Ú', 'Û', 'Ü', 'U', 'U', 'U', 'U', 'U', 'U', 'W', 'Y', 'Ÿ', 'Y', 'Z', 'Z', 'Z', 'à', 'á', 'â', 'ã', 'ä', 'a', 'a', 'a', 'å', 'æ', 'ç', 'd', 'd', 'è', 'é', 'ê', 'ë', 'ƒ', 'g', 'ğ', 'g', 'g', 'h', 'h', 'ì', 'í', 'î', 'ï', 'i', 'i', 'i', 'i', 'ı', 'j', 'k', 'l', 'l', 'l', 'l', 'ñ', 'n', 'n', 'n', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'o', 'o', 'o', 'œ', 'r', 'r', 'r', 's', 'š', 'ş', 't', 't', 'ù', 'ú', 'û', 'ü', 'u', 'u', 'u', 'u', 'u', 'u', 'w', 'ÿ', 'y', 'y', 'z', 'z', 'z', 'ß');
		$replace = array('C', 'c', 'G', 'g', 'i', 'I', 'O', 'o', 'S', 's', 'U', 'u', '-', 'A', 'A', 'A', 'A', 'Ae', 'A', 'A', 'A', 'A', 'A', 'C', 'C', 'C', 'C', 'C', 'D', 'D', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'G', 'G', 'G', 'G', 'H', 'H', 'I', 'I', 'I', 'I', 'I', 'N', 'N', 'N', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'OE', 'R', 'R', 'R', 'S', 'S', 'S', 'S', 'T', 'T', 'T', 'U', 'U', 'U', 'Ue', 'U', 'U', 'U', 'U', 'U', 'U', 'W', 'Y', 'Y', 'Y', 'Z', 'Z', 'Z', 'a', 'a', 'a', 'a', 'ae', 'a', 'a', 'a', 'a', 'ae', 'c', 'd', 'd', 'e', 'e', 'e', 'e', 'f', 'g', 'g', 'g', 'g', 'h', 'h', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'j', 'k', 'l', 'l', 'l', 'l', 'n', 'n', 'n', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'oe', 'r', 'r', 'r', 's', 's', 's', 't', 't', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'w', 'y', 'y', 'y', 'z', 'z', 'z', 'ss');
		$text = str_replace($search, $replace, $text);
		$permalink = preg_replace('/[^a-zA-Z0-9-_]/', '', $text);
		if($options == "nodashes") $permalink = mb_strtolower(strtr($permalink, " ", "æ"));
		else $permalink = mb_strtolower(strtr($permalink, " ", "-"));
		$permalink = str_replace("--", "-", $permalink);
		//$permalink = str_replace("39", "", $permalink);
		return $permalink;
	}

	protected function filter($inputType = "string", $inputText = "defaultText", $options = "") {
		if($inputType == "string") {
			if($inputText===null) return null;
			else return filter_var($inputText, FILTER_SANITIZE_STRING);
		} else if($inputType == "datetime") {
			//echo "gelenlerden:".$inputText; exit();
			if(DateTime::createFromFormat('Y-m-d H:i:s', $inputText) !== false) return $inputText;
			else if(DateTime::createFromFormat('Y-m-d H:i', $inputText) !== false) return $inputText.":00";
			else if(DateTime::createFromFormat('Y-m-d', $inputText) !== false) return $inputText." 00:00:00";
			else return null;
		}  else if($inputType == "date") {
			//echo "gelenlerden:".$inputText; exit();
			if(DateTime::createFromFormat('Y-m-d', $inputText) !== false) return $inputText;
			else return null;
		} else if($inputType == "time") {
			//echo "gelenlerden:".$inputText; exit();
			if(DateTime::createFromFormat('H:i:s', $inputText) !== false) return $inputText;
			if(DateTime::createFromFormat('H:i', $inputText) !== false) return $inputText.":00";
			else return null;
		} else if($inputType == "alphanumeric") {
			return preg_replace('/[^a-zA-Z0-9-_]/', '', $inputText);
		} else if($inputType == "int") {
			if($inputText===null || $inputText==="") return null;
			else return filter_var($inputText, FILTER_VALIDATE_INT);
		} else if($inputType == "float") {
			if($inputText===null) return null;
			else return filter_var($inputText, FILTER_VALIDATE_FLOAT);
		} else if($inputType == "double") {
			if($inputText===null) return null;
			else return filter_var($inputText, FILTER_VALIDATE_FLOAT);
		} else if($inputType == "email") {
			return filter_var($inputText, FILTER_SANITIZE_EMAIL);
		} else if($inputType == "url") {
			return filter_var($inputText, FILTER_SANITIZE_URL);
		} else if($inputType == "html") {
			return filter_var($inputText, FILTER_SANITIZE_MAGIC_QUOTES);
		} else if($inputType == "json") {
			json_decode($inputText);
			if(json_last_error() == JSON_ERROR_NONE) return filter_var($inputText, FILTER_SANITIZE_MAGIC_QUOTES);
			else return null;
		} else if($inputType == "callback") {
			$identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';
			$reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case', 'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 'for', 'switch', 'while', 'debugger', 'function', 'this', 'with', 'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 'private', 'public', 'yield', 'interface', 'package', 'protected', 'static', 'null', 'true', 'false');
			return preg_match($identifier_syntax, $inputText) && !in_array(mb_strtolower($inputText, 'UTF-8'), $reserved_words);
		} else if($inputType == "raw") {
			return $inputText;
		} else if($inputType == "escape") {
			return mysqli_real_escape_string($inputText);
		} else if($inputType == "generic") {
			return filter_var($inputText, FILTER_SANITIZE_MAGIC_QUOTES);
		} else {
			throw new ExceptionLogger("Unknown type for data filter: ".$inputType." for ".$inputText);
		}
	}

	public function castAsObject(array $arr, stdClass $parent = null) {
	    if ($parent === null) {
	        $parent = $this;
	    }

	    foreach ($arr as $key => $val) {
	        if (is_array($val)) {
	            $parent->$key = $this->parse($val, new stdClass);
	        } else {
	            $parent->$key = $val;
	        }
	    }

	    return $parent;
	}

	public function checkAccess($requiredPermission,$userObject=null) {
		//$u = new User();
		//$permissions = $u->getUserPermissions($userObject);

		// Access control is disabled, let it be
		if(!$this->accessControl)
			return true;

		// Get users permissions
		if($userObject==null) {
			// If no user object is passed, get the active user
			global $user;
			
			if($user->signedIn===false)
				return false;

			$activeUser = $user->getActiveUser();

			if(!isset($activeUser["id"]) || !isset($activeUser["userGroup"]["permissions"])) {
				throw new ExceptionLogger("No user object is provided and no active user data found");
				return false;
			} else {
				$userPermissions = $user->getPermissions($activeUser);
			}

		} else {
			// If user object is provided, get its permissions
			$checker = new User();
			$userPermissions = $checker->getPermissions($userObject);
		}

		//echo "userPermissions"; var_dump($userPermissions);

		// Create valid permissions alternatives
		$validPermissions = array();
		$objectArray = explode("#",$requiredPermission);
		if(count($objectArray)>1) $objectID = (int)$objectArray[1];
		$actionArray = explode(".",$objectArray[0]);
		$actionArrayParts = count($actionArray)-1;
		$previous = "";
		for($i=0;$i<$actionArrayParts;$i++) {
			$validPermissions[] = $previous.$actionArray[$i].".*";
			$previous = $actionArray[$i].".";
		}
		$validPermissions[] = $objectArray[0];
		if(isset($objectID)) $validPermissions[] = $objectArray[0]."#".$objectID;
		$validPermissions[] = "*";	

		//echo "validPermissions: "; var_dump($validPermissions);	

		// Check any valid permissions is available in user permissions
		if(is_array($userPermissions)) {	

			if(!empty(array_intersect($userPermissions, $validPermissions))) {
				return true;
			} else if(substr($requiredPermission, -1)=="~") {
				// We are checking for object.~ which means any action permission will be enough
				$length = strpos($requiredPermission,"~");
				foreach($userPermissions as $up) {
					//echo "checking... ".substr($up,0,$length)." ?= ".substr($requiredPermission, 0, $length).PHP_EOL;
					if(substr($up,0,$length)==substr($requiredPermission, 0, $length)) return true;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}

	}

	public function noAccess() {
		throw new Exception("Bu işlem için yetkiniz bulunmamaktadır.");
		return false;
	}

}
?>