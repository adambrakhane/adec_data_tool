<?php

class Db extends Db_MySQL {

	private $env = array(
			'host'	=>	'localhost',
			'dbname'=>	'adeccat',
			'port'	=>	'3306',
			'user'	=>	'D36sy76qK',
			'passwd'=>	'6WQFDBumRMmU9JnT'
			);

	private $schema=array(); // Table names & fields from the database
	
	//public function list($table); // Requires $table
	//public function insert($data, $table);
	//public function update($data, $table); // Requires $data['id'], $table
	//public function delete($data, $table); // Requires $data['id'], $table
	
	function __construct() {
		parent::__construct($this->env); // Connect to PDO
		
		// Get list of tables
		try {
			$sql = "SHOW TABLES";
			$tables = $this->fetchAll($sql,array());
			foreach($tables as $t) {
				$table = $t['Tables_in_adeccat']; // @TODO: This is probably super non portable...
				$this->schema[$table] = array();
				$sql = "SHOW COLUMNS FROM ".$table;
				$fields = $this->fetchAll($sql,array());
				foreach($fields as $f) {
					$this->schema[$table][]=$f["Field"];
				}
			}
		}
		catch (Exception $e) {
			throw new Exception("Could not connect to database. {".$e->getMessage()."}",$e->getCode());
		}
		
		
	}
	
	public function insert($data, $table) {
		if($table === null || !array_key_exists($table,$this->schema)) {
			throw new Exception("Table not supplied or invalid.",0);
		}
		if(is_array($data) && isset($data[0]) && is_array($data[0])) {
			// Multi dim!
			
			$vals = '';
			$params=array();
			foreach($data as $data_chunk) {
				$sql = "INSERT INTO `".$table."` (";
				$cur_vals = "(";
				foreach($data_chunk as $k => $v) {
					$sql .= "`".$k."`, ";
					$cur_vals .= "?, ";
					$params[] = $v;
				}
				$cur_vals = rtrim($cur_vals, ', ');
				$cur_vals .= "), ";
				$vals .= $cur_vals;
			}
			$vals = rtrim($vals, ', ');
			$sql = rtrim($sql, ', ');
			$sql = $sql.") VALUES ".$vals;
			$this->execute($sql,$params);
			return $this->getLastInsertId();
		}
		else {
			
			$sql = "INSERT INTO `".$table."` (";
			$vals = '';
			$params=array();
			foreach($data as $k => $v) {
				$sql .= "`".$k."`, ";
				$vals .= "?, ";
				$params[] = $v;
			}
			$sql = rtrim($sql, ', ');
			$vals = rtrim($vals, ', ');
			$sql = $sql.") VALUES (".$vals.")";
			$this->execute($sql,$params);
			return $this->getLastInsertId();
		}
		
	}
	public function updateOneById($data, $id, $table) {
		// @TODO: instead of just `id`=$id, could turn it into a PK associative array for WHERE match
		if($table === null || !array_key_exists($table,$this->schema)) {
			throw new Exception("Table not supplied or invalid.",0);
		}
		if(is_array($data) && isset($data[0]) && is_array($data[0])) {
			// Multi dim!
			throw new Exception("Multi-dim update not supported",0);
		}
		else {
			
			$sql = "UPDATE `".$table."` SET ";
			$params=array();
			foreach($data as $k => $v) {
				// "`fieldname`=?, "
				$sql .= "`".$k."`=";
				$sql .= "?, ";
				$params[] = $v;
			}
			$sql = rtrim($sql, ', ');
			$sql .= " WHERE `id`=?";
			$params[] = intval($id);
			return $this->execute($sql,$params);
		}
		
	}
	
	public function updateGPS($data,$community_id) {
		$vals = '';
		$params=array();
		foreach($data as $data_chunk) {
			$sql = "INSERT INTO `community_gps` (";
			$cur_vals = "(";
			foreach($data_chunk as $k => $v) {
				$sql .= "`".$k."`, ";
				$cur_vals .= "?, ";
				$params[] = $v;
			}
			$cur_vals = rtrim($cur_vals, ', ');
			$cur_vals .= "), ";
			$vals .= $cur_vals;
		}
		$vals = rtrim($vals, ', ');
		$sql = rtrim($sql, ', ');
		$sql .= ") VALUES ".$vals." ";
		$sql .= "ON DUPLICATE KEY UPDATE `gps_lat`=VALUES(`gps_lat`), `gps_lon`=VALUES(`gps_lon`), `gps_ele`=VALUES(`gps_ele`), `comments`=VALUES(`comments`)";
		return $this->execute($sql,$params);
	}
	public function updateJunta($data,$community_id) {
		$vals = '';
		$params=array();
		foreach($data as $data_chunk) {
			$sql = "INSERT INTO `community_junta` (";
			$cur_vals = "(";
			foreach($data_chunk as $k => $v) {
				$sql .= "`".$k."`, ";
				$cur_vals .= "?, ";
				$params[] = $v;
			}
			$cur_vals = rtrim($cur_vals, ', ');
			$cur_vals .= "), ";
			$vals .= $cur_vals;
		}
		$vals = rtrim($vals, ', ');
		$sql = rtrim($sql, ', ');
		$sql .= ") VALUES ".$vals." ";
		$sql .= "ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `phone`=VALUES(`phone`), `comments`=VALUES(`comments`)";
		return $this->execute($sql,$params);
	}
	public function updateEval($data,$community_id) {
		$vals = '';
		$params=array();
		foreach($data as $data_chunk) {
			$sql = "INSERT INTO `data` (";
			$cur_vals = "(";
			foreach($data_chunk as $k => $v) {
				$sql .= "`".$k."`, ";
				$cur_vals .= "?, ";
				$params[] = $v;
			}
			$cur_vals = rtrim($cur_vals, ', ');
			$cur_vals .= "), ";
			$vals .= $cur_vals;
		}
		$vals = rtrim($vals, ', ');
		$sql = rtrim($sql, ', ');
		$sql .= ") VALUES ".$vals." ";
		$sql .= " ON DUPLICATE KEY UPDATE
		`question_id`=VALUES(`question_id`),
		`recorded_by_id`=VALUES(`recorded_by_id`),
		`recorded_date`=VALUES(`recorded_date`),
		`response`=VALUES(`response`),
		`comments`=VALUES(`comments`) ";
		//$sql .= "ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `phone`=VALUES(`phone`), `comments`=VALUES(`comments`)";
		//return $sql;
		return $this->execute($sql,$params);
	}
	
	public function listAll($table=null,$mode='all',$groupby='',$orderby='') {
		$out = array();
		if($table === null || !array_key_exists($table,$this->schema)) {
			throw new Exception("Table not supplied or invalid.",0);
		}
		$groupby = ($groupby!='' ? " GROUP BY `".$groupby."`" : '' );
		$orderby = ($orderby!='' ? " ORDER BY `".$orderby."`" : '' );
		$sql = "SELECT * FROM `".$table."`".$groupby.$orderby;
		//$sql = ($mode=='latest' ? $sql." ORDER BY `id` DESC LIMIT 5" : $sql );
		$out = $this->fetchAll($sql,array());
		return $out;
	}
	public function listAllGPS($community_id,$table=null,$mode='all',$groupby='',$orderby='') {
		$out = array();
		if($table === null || !array_key_exists($table,$this->schema)) {
			throw new Exception("Table not supplied or invalid.",0);
		}
		$groupby = ($groupby!='' ? " GROUP BY `".$groupby."`" : '' );
		$orderby = ($orderby!='' ? " ORDER BY `".$orderby."`" : '' );
		$sql = "SELECT * FROM `".$table."` WHERE `community_id`=?".$groupby.$orderby;
		//$sql = ($mode=='latest' ? $sql." ORDER BY `id` DESC LIMIT 5" : $sql );
		$out = $this->fetchAll($sql,array($community_id));
		return $out;
	}
	public function listAllJunta($community_id,$table=null,$mode='all',$groupby='',$orderby='') {
		$out = array();
		if($table === null || !array_key_exists($table,$this->schema)) {
			throw new Exception("Table not supplied or invalid.",0);
		}
		$groupby = ($groupby!='' ? " GROUP BY `".$groupby."`" : '' );
		$orderby = ($orderby!='' ? " ORDER BY `".$orderby."`" : '' );
		$sql = "SELECT * FROM `".$table."` WHERE `community_id`=?".$groupby.$orderby;
		//$sql = ($mode=='latest' ? $sql." ORDER BY `id` DESC LIMIT 5" : $sql );
		$out = $this->fetchAll($sql,array($community_id));
		return $out;
	}
	
	public function getDepartments($cr_id=null) {
		$out = array();
		$ar = array();
		$table = "communities";
		$where = '';
		if($cr_id!=null) {
			$where = " WHERE `circuit_rider_id`=? ";
			$ar[] = intval($cr_id);
		}
		$orderby = " GROUP BY `department` ORDER BY `department` ";
		$sql = "SELECT * FROM `".$table."` ".$where.$orderby;
		//$sql = ($mode=='latest' ? $sql." ORDER BY `id` DESC LIMIT 5" : $sql );
		$out = $this->fetchAll($sql,$ar);
		return $out;
	}
	public function getCircuitRiders() {
		$out = array();
		$ar = array();
		$sql = "SELECT id, first_name, last_name, role FROM `circuit_riders` ";
		$out = $this->fetchAll($sql,$ar);
		return $out;
	}
	public function getMunicipalities($dept,$cr_id=null) {
		$out = array();
		$ar = array();
		$table = "communities";
		$where = '';
		
		if(($dept == null)) { // ALL!!!
			//$where = " WHERE `circuit_rider_id`=?";
		}
		else if($cr_id!=null) {
			$where = " WHERE `circuit_rider_id`=? AND `department`=?";
			$ar[] = intval($cr_id);
			$ar[] = $dept;
		}
		else {
			$where = " WHERE `department`=?";
			$ar[] = $dept;
		}
		$orderby = " GROUP BY `municipality` ORDER BY `municipality` ";
		$sql = "SELECT * FROM `".$table."` ".$where.$orderby;
		//$sql = ($mode=='latest' ? $sql." ORDER BY `id` DESC LIMIT 5" : $sql );
		$out = $this->fetchAll($sql,$ar);
		return $out;
	}
	public function getCommunities($dept=null,$municip=null,$cr_id=null) {
		$out = array();
		$ar = array();
		$table = "communities";
		$where = '';
		if(($dept == null || $municip == null) && $cr_id!=null) { // Search by circuit rider only
			$where = " WHERE `circuit_rider_id`=?";
			$ar[] = $cr_id;
		}
		else if(($dept == null || $municip == null) && $cr_id==null) { // No search
			$where = " ";
			$ar[] = $cr_id;
		}
		else if($cr_id!=null) {
			$where = " WHERE `circuit_rider_id`=? AND `department`=? AND `municipality`=?";
			$ar[] = intval($cr_id);
			$ar[] = $dept;
			$ar[] = $municip;
		}
		else {
			$where = " WHERE `department`=? AND `municipality`=?";
			$ar[] = $dept;
			$ar[] = $municip;
		}
		$orderby = " ORDER BY `community` ";
		$sql = "SELECT * FROM `".$table."` ".$where.$orderby;
		//$sql = ($mode=='latest' ? $sql." ORDER BY `id` DESC LIMIT 5" : $sql );
		$out = $this->fetchAll($sql,$ar);
		return $out;
	}
	
	
	public function getOne($table=null,$id=null) {
		$out = array();
		if($table === null || !array_key_exists($table,$this->schema) || $id===null) {
			throw new Exception("Table or id not supplied or invalid.",0);
		}
		
		$sql = "SELECT * FROM `".$table."` WHERE id=? LIMIT 1";
		$params=array($id);
		$data = $this->fetchAll($sql,$params);
		if(sizeof($data)==1)
			return $data[0];
		return array();
	}
	
	public function verifyUserPassword($circuit_rider_id,$pt_password) {
		
		$sql = "SELECT password FROM `circuit_riders` WHERE id=? LIMIT 1";
		$params=array($circuit_rider_id);
		$data = $this->fetchAll($sql,$params);
		if(sizeof($data)==1) {
			if($data[0]["password"]==hash('md5',$pt_password)) {
				return true;
			}
		}
		return false; // Why didn't we get a user?
	}
	
	public function date_of_data($community_id) {
		$sql = "select recorded_date from data WHERE community_id=?	ORDER BY recorded_date DESC LIMIT 1";
		$params=array($community_id);
		$data = $this->fetchAll($sql,$params);
		if(sizeof($data)==1)
			return $data[0];
		return array();
	}
	public function dates_of_data($community_id) {
		$sql = "select recorded_date from data WHERE community_id=?	GROUP BY recorded_date ORDER BY recorded_date DESC";
		$params=array($community_id);
		$data = $this->fetchAll($sql,$params);
		if(sizeof($data)>=1)
			return $data;
		return array();
	}
	// This returns the newest version of every question
	public function latestDataFull($community_id) {
		$sql = "SELECT `data`.* FROM ( SELECT * FROM `data` ORDER BY `data`.recorded_date DESC ) AS `data` WHERE `data`.community_id=? GROUP BY `data`.question_id ORDER BY `data`.question_id ASC;";
		$params=array($community_id);
		$data = $this->fetchAll($sql,$params);
		if(sizeof($data)>=1)
			return $data;
		return array();
	}
	// This finds the newest date recorded and returns all the data for that date
	public function latestData($community_id) {
		$sql = "SELECT `data`.* FROM `data` WHERE `data`.community_id=? AND `recorded_date` = (SELECT recorded_date FROM `data` WHERE `data`.community_id=? ORDER BY `data`.recorded_date DESC LIMIT 1) ORDER BY `data`.question_id ASC;";
		$params=array($community_id,$community_id);
		$data = $this->fetchAll($sql,$params);
		if(sizeof($data)>=1)
			return $data;
		return array();
	}
	
	public function dataByDate($community_id,$recorded_date) {
		$sql = "SELECT `data`.* FROM `data`
WHERE `data`.community_id=? AND `recorded_date`=?
ORDER BY `data`.question_id ASC;";
		$params=array($community_id,$recorded_date);
		$data = $this->fetchAll($sql,$params);
		if(sizeof($data)>=1)
			return $data;
		return array();
	}
	
	public function deleteOne($table=null,$id=null) {
		$out = array();
		if($table === null || !array_key_exists($table,$this->schema) || $id===null) {
			throw new Exception("Table or id not supplied or invalid.",0);
		}
		
		$sql = "DELETE FROM `".$table."` WHERE id=? LIMIT 1";
		$params=array($id);
		return $this->execute($sql,$params);
	}
	// Delete WHERE associative key names (and their values) match columns in the db
	public function deleteByKeys($table=null,$keys) {
		$out = array();
		if($table === null || !array_key_exists($table,$this->schema)) {
			throw new Exception("Table or id not supplied or invalid.",0);
		}
		$where = " WHERE ";
		$params=array();
		foreach($keys as $col_name => $val) {
			$where .= "`".$col_name."`=? AND ";
			$params[] = $val;
		}
		$where = rtrim($where, ' AND ');
		$sql = "DELETE FROM `".$table."` ".$where."";
		return $this->execute($sql,$params);
	}
	public function deleteGPS($community_id, $location_name) {
		
		$sql = "DELETE FROM `community_gps` WHERE `community_id`=".$community_id." AND `location_name` IN (";
		foreach($location_name as $n) {
			$sql .= "?, ";
			$params[] = $n;
		}
		$sql = rtrim($sql, ', ');
		$sql .= ")";

		return $this->execute($sql,$params);
	}
	public function deleteJunta($community_id, $role) {
		
		$sql = "DELETE FROM `community_junta` WHERE `community_id`=".$community_id." AND `role` IN (";
		foreach($role as $n) {
			$sql .= "?, ";
			$params[] = $n;
		}
		$sql = rtrim($sql, ', ');
		$sql .= ")";

		return $this->execute($sql,$params);
	}
	public function deleteData($community_id, $recorded_date, $question_id) {
		$params = array();
		$sql = "DELETE FROM `data` WHERE `community_id`=? AND `recorded_date`=? AND `question_id` IN (";
		$params[] = $community_id;
		$params[] = $recorded_date;
		foreach($question_id as $n) {
			$sql .= "?, ";
			$params[] = $n;
		}
		$sql = rtrim($sql, ', ');
		$sql .= ")";

		return $this->execute($sql,$params);
	}
	
	public function search($table=null,$search_text) {
		$out = array();
		if($table === null || !array_key_exists($table,$this->schema)) {
			throw new Exception("Table {".$table."} not supplied or invalid.",0);
		}
		$sql = "SELECT * FROM `".$table."` WHERE ";
		$params=array();
		foreach($this->schema[$table] as $field) {
			$sql .= $field.' LIKE ? || ';
			$params[]="%".$search_text."%";
		}
		$sql = rtrim($sql,'|| ');
		//$sql = ($mode=='latest' ? $sql." ORDER BY `id` DESC LIMIT 5" : $sql );
		$out = $this->fetchAll($sql,$params);
		return $out;
	}
	
	// Return array of rows that don't exist
	public function rows_exist($data, $table=null) {
		if($table === null || !array_key_exists($table,$this->schema)) {
			throw new Exception("Table not supplied or invalid.",0);
		}
		$sql = "SELECT id FROM `".$table."` WHERE id IN (";
		$vals = '';
		$params=array();
		foreach($data as $k => $v) {
			$sql .= "?, ";
			$params[] = $v;
		}
		$sql = rtrim($sql, ', ');
		$sql = $sql.')';
		
		return $this->fetchAll($sql,$params);
	}
	
	public function getTableSchema($table) {
		if(array_key_exists($table,$this->schema))
			return $this->schema[$table];
		return array();
	}
	
}