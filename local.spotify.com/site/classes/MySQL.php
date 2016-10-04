<?php
/*
 * MySQL class
 * for MySQLi Extension of PHP
 * by fiatux, 2014
 * @package contentmankit
 */

class MySQL {
	
	private $databaseName;
	private $tableName;
	
	private $username;
	private $password;
	
	private $host;
	private $port=3306;
	
	private $identifier;
	private $instance;
	private $mysqli;
	
	private $siteID;
	
	private $caching;
	private $cacheTime;
	private $memcached;
	private $memcache;
	private $cacheServer="127.0.0.1";
	private $cachePort=11211;
	
	private $security=false;
	private $debug = false;
	
	private $conditions = array();
	private $limit;
	private $paging;
	private $sorting;
	private $order;
	
	private $count;
	private $error;
	private $affectedRowCount;
	
	private $transactionInProgress;
	private $connected = false;
	
	private $lastInsertID;
	private $results;
	
	private $insertReplace=false;
	private $insertIgnore=false;
	
	private $updateOnPrimaryKeyUpdates = array();
	private $updateOnPrimaryKeyPrimaryKey = array();

	public function __construct() {

	}
	
	/*
	 * Sets database hostname
	 * @param string $host server hostname
	 * @return bool
	 */
	public function setHost($host="localhost") {
		$this->host = $host;
		return true;
	}
	
	/*
	 * Sets database server port
	 * @param string $port server hostname
	 * @return bool
	 */
	public function setPort($port="3306") {
		$this->port = $port;
	}
	
	/*
	 * Sets database table
	 * @param string $name name of the table
	 * @return bool 
	 */
	public function setTable($name) {
		$this->tableName = $name;
		return true;
	}
	
	/*
	 * Sets database for queries
	 * @param string $name name of the database
	 * @return bool 
	 */
	public function setDatabase($name) {
		
		if(!isset($this->host) || !isset($this->username) || !isset($this->password)) {
			$this->error = "Please first set host, username and password";
			return false;
		}
		
		$this->databaseName = $name;	
        if($this->connected)
        {	
            if(@mysqli_close($this->mysqli)) {
                $this->connected = false;
                $this->results = null;
                $this->connect();
            }
        } else {
            $this->connect();
        }
		
		return true;
	}
	
	/*
	 * Sets database user for connection
	 * @param string $user name of the user
	 * @return bool 
	 */
	public function setUser($user) {
		$this->username = $user;
		return true;
	}
	
	/*
	 * Sets database password for connection
	 * @param string $password password for the database connection
	 * @return bool 
	 */
	public function setPassword($password) {
		$this->password = $password;
		return true;
	}
	
	/*
	 * Sets debugging option
	 * @param bool $bool true or false
	 * @return bool 
	 */
	public function setDebug($bool) {
		$this->debug = (bool)$bool;
		return true;
	}
	
	/*
	 * Sets caching option
	 * @param bool $bool true or false
	 * @return bool 
	 */
	public function setCaching($caching) {
		$this->memcached = new Memcached;
		
		if(!isset($this->siteID)) $this->siteID = "YOYO";
		
		if($caching) {
			if($this->memcached->addServer($this->cacheServer,$this->cachePort)) {
				$this->caching = true;
			} else {
				if($this->debug) $this->debugError("Cannot connect to memcached");
				$this->caching = false;
				unset($this->memcached);
			} 
		} else {
			$this->caching = (bool)$caching;
			unset($this->memcached);
		}
		
		return true;
	}
	
	/*
	 * Flushes database cache mechanism
	 * @return bool 
	 */
	public function flushCache() {
		if(isset($this->memcached)) {
			if($this->memcached->flush()) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	
	/*
	 * Sets default cache time
	 * @param int $cacheTime cache time in seconds
	 * @return bool 
	 */
	public function setCacheTime($cacheTime) {
		$this->cacheTime = (int)$cacheTime;
		return true;
	}
	
	/*
	 * Sets security option, queries are escaped in this is turned on
	 * @param bool $security true or false
	 * @return bool 
	 */
	public function setSecurity($security) {
		$this->security = (bool)$security;
		return true;
	}
	
	/*
	 * Connects to MySQL intances with user and object details set before calling this
	 * @return bool 
	 */
	public function connect() {
		$this->mysqli = @new mysqli($this->host, $this->username, $this->password, $this->databaseName, $this->port);
        if($this->mysqli->connect_errno) {
        	$this->error = "#".$this->mysqli->connect_errno.": ".$this->mysqli->connect_error;
			if($this->debug) $this->debugError($this->error);
			throw new ExceptionLogger("Cannot connect to MySQL database: ".$this->error);
			return false;
        }
		$this->mysqli->set_charset('utf8');
		$this->connected = true;
		return true;
	}
	
	/*
	 * Sets insert replacer option
	 * @param bool $insert true or false
	 * @return bool 
	 */
	public function setInsertReplacer($insert) {
		$this->insertReplacer = (bool)$insert;
	}
	
	/*
	 * Inserts row in MySQL tbale
	 * @param string $tableName MySQL table name
	 * @param array $data Data to be inserted
	 * @return int get insert ID is returned 
	 */
	public function insert($tableName,$data) {
		$success = false;
		
		if($this->insertReplace) $query = 'REPLACE';
        else $query = 'INSERT';
		
		if($this->insertIgnore) $query.=' IGNORE';
		
		$query.= ' INTO '.$tableName;
		
		if(is_array($data)) { 
			$columns = array();
			$values = "";
			if(count($data)>0) {
				$counter = 0;
				foreach($data as $key=>$value) {
					$columns[] = $this->escape($key);
					if($counter>0) $values.=",";
					if($value===null) $values.= 'null';
					else if($this->security) $values.= '"'.$this->escape($value).'"';
					else $values.= '"'.$value.'"';
					$counter++;
				}
				
				$query.= "(".implode(', ',$columns).")";
				$query.= " VALUES (".$values.")";
				
				if(isset($this->updateOnPrimaryKeyUpdates) && isset($this->updateOnPrimaryKeyPrimaryKey)) {
					if(count($this->updateOnPrimaryKeyUpdates)>0) {
						$j=0;
						$query.=' ON DUPLICATE KEY UPDATE '.$this->updateOnPrimaryKeyPrimaryKey.'=LAST_INSERT_ID('.$this->updateOnPrimaryKeyPrimaryKey.')';
						foreach($this->updateOnPrimaryKeyUpdates as $column=>$value) {
							$query.=', '.$column.'=';
							if($value===null) $query.= 'null';
							else if($this->security) $query.= '"'.$this->escape($value).'"';
							else $query.= '"'.$value.'"';
						}
					}
				}
				
				if($this->debug) $this->debugQuery($query);
				
				if(!$this->mysqli->query($query)){
					$this->error = $this->mysqli->error;
					if($this->debug) $this->debugError($this->error);
					throw new ExceptionLogger($this->mysqli->error);
					return false;
				} 
				
			} else {
				throw new ExceptionLogger("Empty array provided for database insert operation.");
				return false;
			}
		} else {
			throw new ExceptionLogger("Provided data for insert operation is not an array.");
			return false;
		}
		
		$this->lastInsertID = $this->mysqli->insert_id;
		$this->affectedRowCount = mysqli_affected_rows($this->mysqli);
		return true;
	}

	/*
	 * Executes MySQL query
	 * @param string $query Query to be executed directly
	 * @return string query result
	 */
	public function run($query) {
		//echo "gelenlerden: ".$query; exit();
		$result = $this->mysqli->query($query);
		if(!$result) {
			$this->error = $this->mysqli->error;
			throw new ExceptionLogger($this->mysqli->error);
		}
		return $result;
	}

	/*
	 * Set Update On Primary Key options
	 * @param query $update query to be executed as an update
	 * @param string $primaryKey primary key field to be checked as a condition
	 * @return string query result
	 */
	public function setUpdateOnPrimaryKey($update,$primaryKey) {
		$this->updateOnPrimaryKeyUpdates = $update;
		$this->updateOnPrimaryKeyPrimaryKey = $primaryKey;
	}

	/*
	 * Updates MySQL row(s)
	 * @param string $tableName MySQL table to be updated
	 * @param array $changes Array of fields to be updated with field names as key, and values as array values.
	 * @return bool 
	 */
	public function update($tableName,$changes,$conditions) {
		if(!$this->connected) return false;
		$query = "UPDATE ".$this->escape($tableName);
		if(is_array($changes)) {
			if(count($changes)>0) {
				$query.=" SET";
				$counter = 0;
				foreach($changes as $column=>$value) {
					if($counter>0) $query.=", ";
					$query.=" ".$this->escape($column)."=";
					if($value===null) $query.= 'null';
					else if($this->security) $query.= '"'.$this->escape($value).'"';
					else $query.= '"'.$value.'"';
					$counter++;
				}
				
				if(is_array($conditions)) {
					$query.=" WHERE ";
					$counter = 0;
					foreach($conditions as $column=>$value) {
						if($counter>0) $query.=" AND";
						$query.=" ".$this->escape($column)."=";
						if($value===null) $query.= 'null';
						else if($this->security) $query.= '"'.$this->escape($value).'"';
						else $query.= '"'.$value.'"';
						$counter++;
					}
					
				} else {
					$query.=" WHERE ".$conditions;
				}

				//echo $query; exit();
				
				if($this->debug) $this->debugQuery($query);
					
				if(!$this->mysqli->query($query)){
					$this->error = $this->mysqli->error;
					throw new ExceptionLogger($this->error);
					if($this->debug) $this->debugError($this->error);
					return false;
				} 
				
				return true;
				
			} else {
				$this->error = "No changes passed";
				throw new ExceptionLogger("No changes passed for database update.");
				return false;
			}
		} else {
			$this->error = "Changes must be in array format";
			throw new ExceptionLogger("Changes must be in array format for update query");
			return false;
		}
	}
	
	/*
	 * Deletes a single MySQL row
	 * @param string $tableName MySQL table to be updated
	 * @param array $condition Array of fields to be checked as conditions
	 * @return bool 
	 */
	public function delete($tableName,$condition) {
		if(!$this->connected) return false;
		if(is_array($condition)) {
			$i=0;
			$where = "";
			foreach($condition as $key=>$value) {
				if($i>0) $where.=" AND";
				$where.= " ".$key."='".$this->escape($value)."'"; 
				$i++;
			}
		} else {
			$where = $this->escape($condition);
		}
		$query = "DELETE FROM ".$this->escape($tableName)." WHERE ".$where." LIMIT 1";
		if($this->debug) $this->debugQuery($query);
		if(!$this->mysqli->query($query)){
			$this->error = $this->mysqli->error;
			if($this->debug) $this->debugError($this->error);
			throw new ExceptionLogger($this->mysqli->error);
			return false;
		}
		return true;
	}
	
	/*
	 * Deletes matched MySQL row(s)
	 * @param string $tableName MySQL table to be updated
	 * @param array $condition Array of fields to be checked as conditions
	 * @return bool 
	 */
	public function deleteAll($tableName,$condition=null) {
		if(!$this->connected) return false;
		$query = "DELETE FROM ".$this->escape($tableName);
		if(isset($condition)) {
			if(is_array($condition)) {
				$query.=" WHERE";
				$counter = 0;
				foreach($condition as $column=>$value) {
					if($counter>0) $query.=" AND";
					$query.=" ".$column."=";
					if($value===null) $query.= 'null';
					else if($this->security) $query.= '"'.$this->escape($value).'"';
					else $query.= '"'.$value.'"';
					$counter++;
				}
			} else {
				$query.=" WHERE ".$condition;		
			}
		}
		
		if($this->debug) $this->debugQuery($query);
		
		if(!$this->mysqli->query($query)){
			$this->error = $this->mysqli->error;
			if($this->debug) $this->debugError($this->error);
			throw new ExceptionLogger($this->mysqli->error);
			return false;
		}
		return true;
	}
	
	/*
	 * Close MySQL connection
	 * @return bool 
	 */
	public function disconnect() {
		return mysqli_close($this->mysqli);
	}
	
	/*
	 * Returns MySQL connection
	 * @return instance 
	 */
	public static function getInstance() {
        return $this->mysqli;
    }
	
	/*
	 * MySQL query
	 * @param string $query MySQL query to be executed to select rows
	 * @param int $cacheTime Cache time to apply on result if caching option is active 
	 * @return bool 
	 */
	public function select($query,$cacheTime=false) { 
		if(!$this->connected) {
			throw new ExceptionLogger("There is no database connection for a select query.");
			return false;
		}
		
		$this->results = null;
		if($this->debug) $this->debugQuery($query);
		
		if($this->caching && isset($this->memcached) && $cacheTime>0) {
			$cacheKey = $this->siteID.md5($query);
			$cached = $this->memcached->get($cacheKey);
			if($cached!==false) {
				$this->results = $cached;
				//echo "from cache:"; var_dump($cached); exit();
				return true;
			}
		}
		$result = $this->mysqli->query($query);
		if(!$result){
			$this->error = $this->mysqli->error;
			throw new ExceptionLogger("Database query error: ".$this->mysqli->error. ". Query was: ".$query);
			if($this->debug) $this->debugError($this->error);
			return false;
		}

		$this->results = $result->fetch_all(MYSQLI_ASSOC);
		
		if($this->caching && isset($this->memcached) && $cacheTime>0) {
			$cacheKey = $this->siteID.md5($query);
			if(!$this->memcached->set($cacheKey,$this->results,$cacheTime) && $this->debug) {
				$this->debugError("Memcached set is not working: ".$this->memcached->getResultCode());
			}
			
		}

		$this->affectedRowCount = mysqli_affected_rows($this->mysqli);
		mysqli_free_result($result);
		return true;
	}
	
	/*
	 * Returns affacted row count in the query
	 * @return int
	 */
	public function getAffectedRowCount() {
		return $this->affectedRowCount;
	}
	
	/*
	 * Returns select query results
	 * @return array
	 */
	public function getAll() {
		return $this->results;
	} 
	
	/*
	 * Returns the first result of the select query
	 * @return array 
	 */
	public function getOne() {
		$temp = $this->results;
		if(isset($temp[0])) return $temp[0];
		else return null;
	}
	
	/*
	 * Returns the last insert ID from the last insert operation
	 * @return int 
	 */
	public function getInsertID() {
		return $this->lastInsertID;
	}
	
	/*
	 * Check if the provided data exists in the table
	 * @param string $tableName Table to be checked for the data
	 * @param array $condition Conditions to be checked in the table
	 * @return array 
	 */
	public function checkForEntry($tableName,$condition) {
		//TODO
	}
	
	/*
	 * Prepares MySQL query
	 * @param string $query MySQL query to be prepared
	 * @return result
	 */
	protected function prepare($query) {
        if (!$stmt = $this->mysqli->prepare($this->_query)) {
            trigger_error("Problem preparing query ($this->query) " . $this->mysqli->error, E_USER_ERROR);
        }
        return $stmt;
    }
	
	/*
	 * Starts MySQL transaction
	 */
	public function startTransaction() {
        $this->mysqli->autocommit (false);
        $this->transactionInProgress = true;
        register_shutdown_function (array ($this, "transactionStatusCheck"));
    }
	
	/*
	 * Commits to the MySQL transaction in progress
	 */
	public function commit () {
        $this->mysqli->commit ();
        $this->transactionInProgress = false;
        $this->mysqli->autocommit (true);
    }
	
	/*
	 * Rollbacks MySQL transaction
	 */
	public function rollback () {
      $this->mysqli->rollback ();
      $this->transactionInProgress = false;
      $this->mysqli->autocommit (true);
    }
	
	/*
	 * Makes transaction status check
	 */
	public function transactionStatusCheck () {
        if (!$this->transactionInProgress)
            return;
        $this->rollback ();
    }
	
	/*
	 * Returns time on server
	 * @return float
	 */
	public function getMicroTime() {
    	list($usec, $sec) = explode(" ",microtime());
    	return ((float)$usec + (float)$sec);
    }
	
	/*
	 * Escapes provided query
	 * @param string $str MySQL query to be escaped
	 * @return string 
	 */
	public function escape($str) {
        return $this->mysqli->real_escape_string($str);
    }
    
	/*
	 * Returns the last error saved
	 * @return string
	 */
    public function getError() {
    	return $this->error;
    }
	
	/*
	 * Debugs MySQL query if SQL Formatter is provided
	 * @param string $query Query to be debugged
	 * @return string 
	 */
	public function debugQuery($query) {
		$sqlFormatter = '../libraries/sqlformatter/SqlFormatter.php';
		if(file_exists($sqlFormatter)) {
			require_once($sqlFormatter);
			echo "here";
			echo SqlFormatter::format($query);
		} else {
			print '<pre>TABLE: '.$this->tableName.'\nQUERY: '.$query.'</pre>';	
		}
		
	}
	
	/*
	 * Error display or sending
	 * @param string $error Error to be sent or display
	 * @return string
	 */
	public function debugError($error) {
		echo $error;
	}
	
	/*
	 * Adds where condition for a MySQL query
	 * @param string $condition 
	 * @param string $operator
	 */
	public function where($condition,$operator=null) {
		if(!in_array(strtoupper($operator), array(null,"AND","OR"))) {
			die("Invalid operator provided for WHERE clause.");
		}
		
		if(is_array($condition)) {
			die("Invalid condition provided for WHERE clause.");
		}
		
		$this->conditions[] = array($operator=>$condition);
		
	}
	
	/*
	 * Builds where condition from provided conditions
	 * @return string
	 */
	public function buildConditions() {
		echo '<pre>'; var_dump($this->conditions); echo '</pre>'; 
		$where = " WHERE";
		if(count($this->conditions)>1) {
			foreach($this->conditions as $counter=>$c) {
				if($counter>0) {
					if(array_keys($c)[0]=="") {
						$where.=" AND";
					} else {
						if($this->security) $where.=" ".$this->escape(array_keys($c)[0]);
						else $where.=" ".array_keys($c)[0];
					}
				}
				$where.= " ".array_values($c)[0];
			}
		} else if(count($this->conditions)==1) {
			$where.= " ".array_values($this->conditions[0])[0]; 
		} else {
			return null;
		} 
		return $where;
		
	}
	
	/*
	 * Gets specific fields from the table set before this operation
	 * @param string $columns Field names comma separated
	 * @param int $limit Number of rows to get
	 * @param int $paging Number of 
	 */
	public function get($columns,$limit=null,$paging=null) {
		if(!isset($this->tableName)) {
			$this->error = "Table name is not set for the query";
			return false;
		}
		if(is_array($columns)) {
			$columns = implode(",",$columns);
		}
		$query = "SELECT ".$this->escape($columns)." FROM ".$this->tableName;
		$conditions = $this->buildConditions();
		if(isset($conditions)) $query.=$conditions;
		if(!isset($limit) && isset($this->limit)) {
			$limit = $this->limit;
		}
		if(isset($limit)) {
			$query.= " LIMIT ";
			if(!isset($paging) && isset($this->paging)) {
				$paging = $this->paging;
			}
			if($paging>0) {
				$paging = (int)$paging;
			} else {
				$paging = 0;
			}
			$query.= $paging.",".$limit;
		}
		if($this->debug) $this->debugQuery($query);
		if(!$result = $this->mysqli->query($query)){
			$this->error = $this->mysqli->error;
			throw new ExceptionLogger($this->mysqli->error);
			if($this->debug) $this->debugError($this->error);
			return false;
		}
		$this->results = $result->fetch_all(MYSQLI_ASSOC);
		$this->affectedRowCount = mysqli_affected_rows($this->mysqli);
		mysqli_free_result($result);
		return $this->getAll();
	}
	
	/*
	 * Resets query parameters
	 * @return bool
	 */
	public function newQuery() {
		$this->conditions = array();
		$this->results = array();
		unset($this->tableName);
		return true;
	}
	
}
?>