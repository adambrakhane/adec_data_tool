<?php
abstract class Db_MySQL {

    protected $_pdo = null; // Hold PDO connection
    protected $_sql = ""; // Hold prepared SQL
    protected $_prepared = null; // Hold PDO statement
    protected $_openCursor = false; // Is the cursor open?
    protected $_env = null; // Hold enviroment. Array keys: host, dbname, port, user, passwd
    

 	public function __construct($env = null){
		if($env === null){
			throw new Exception("No database environment supplied",1001);
		}
		$this->_env = $env; // Store environment
		// Try to open connection
		$dsn = "mysql:host=".$this->_env['host'].";dbname=".$this->_env['dbname'].";port=".$this->_env['port'];
		try {
			$this->_pdo = new PDO($dsn, $this->_env['user'], $this->_env['passwd']);
			$this->execute('SET NAMES "UTF8"',array());
		}catch(PDOException $e){
			throw new Exception("Unable to connect to database! {".$e->getMessage()."}",1001);
		}

		// Set PDO to error out with exceptions
		if(!$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)){
			throw new Exception("Unable to set PDO error mode!",1002);
        }
 	}

 	public function __destruct(){
 		unset($this->_pdo);
 	}

 	

 	protected function _closeCursor(){
 		if($this->_prepared !== null && $this->_openCursor === true){
 			
 			# a PDO preparedStatement exists, and has an open cursor
 			$result = $this->_prepared->closeCursor();	

	    	if($result  === false){
	        	throw new Exception("Unable to close the cursor!",1003);
	    	}
	    	
	    	$this->_openCursor = false;
 		}
 	}

	/**
	 * _prepare does several things, it actually prepares sql so it can be 
	 * executed, it also makes sure the cursor is closed on old sql requests, 
	 * and updates the nessisary flags.
	 * 
 	 * @author Daniel Sherman 11/7/2011
 	 * @param string $sql The sql you want to be prepared for execution
 	 * @throws Wri_Db_Exception
 	 * @uses $_prepared
 	 * @uses $_sql
 	 * @uses $_pdo
 	 * @uses _closeCursor
	 */
 	protected function _prepare($sql = ''){
 		if($this->_prepared !== null){
 			# a PDO preparedStatement exists
 			if($this->_sql !== $sql){
 				# somone wants to process a new query so we need to do some book keeping
 				$this->_closeCursor();
 				unset($this->_prepared);
 			}else{
 				#someone wants to re-run the current query
 				$this->_closeCursor();
 				return;
 			}
 		}
 		
 		# Time to prepare the new sql so it can be exectuted
 		try{
            $this->_prepared = $this->_pdo->prepare($sql);
            $this->_sql = $sql;
        } catch(PDOException $e){
            # unable to prepare statement, thus throw an exception
            $ar = array(
            	'mysqlErrorCode' => $e->getCode(), 
				'mysqlErrorMsg' => $e->getMessage()
			);
			throw new Exception("Unable to prepare PDO statement",1004);
        }
 	}

 	/**
 	 * The execute method takes a chunk of sql and parameters to be inserted into
 	 * it, and prpares it, executes it.
 	 * 
 	 * @author Daniel Sherman 11/7/2011
 	 * @param string $sql The sql you want to be prepared for execution
	 * @param array $params the parameters that need to be inserted into $sql
	 * @throws Wri_Db_Exception
     * @uses _prepare()
     * 
 	 */
    function execute($sql, $params=array()){
    	$this->_prepare($sql);

        try {
            return $this->_prepared->execute($params);
        } catch (PDOException $e) {
            # unable to ececute the query, thus throw an exception
            $ar = array(
            	'mysqlErrorCode' => $e->getCode(), 
				'mysqlErrorMsg' => $e->getMessage()
			);
			throw new Exception($e->getMessage(),$e->getCode());
        }
		
    }

 	/**
 	 * This method gets the last insert id from the database.
 	 * 
 	 * @author Daniel Sherman 11/7/2011
	 * @return integer the last insert id
	 * @throws Wri_Db_Exception
 	 * @uses $_pdo
 	 */
 	function getLastInsertId(){
        try {
           return $this->_pdo->lastInsertId();
        } catch (PDOException $e) {
            # unable to get an id
            $ar = array(
            	'mysqlErrorCode' => $e->getCode(), 
				'mysqlErrorMsg' => $e->getMessage()
			);
			throw new Exception("Couldn't get ID",1006);
		}
	}

 	/**
 	 * fetchAll is a wrapper designed to make writting child classes very easy.
 	 * 
 	 * 99% of the time when you guery the database, you want all the results back.
 	 * fetchAll takes care of this by handling preparing & executing the query 
	 * for you.
 	 * 
 	 * @author Daniel Sherman 11/7/2011
	 * @return array the results of the query
	 * @throws Wri_Db_Exception
 	 * @uses $_prepared
 	 * @uses execute
 	 */ 	
 	function fetchAll($sql = '', $params=array(), $style = PDO::FETCH_ASSOC){
 		$this->execute($sql, $params);

        try {
           return $this->_prepared->fetchAll($style);
        } catch (PDOException $e) {
            # unable to get results
            $ar = array(
            	'mysqlErrorCode' => $e->getCode(), 
				'mysqlErrorMsg' => $e->getMessage()
			);
			throw new Exception("Unable to fetch all the results!",1007);
        }
 	}

 	/**
 	 * fetch makes it east to get a simple result set from the database, it needs 
	 * to be used in conjunction with execute().
 	 * 
 	 * @author Daniel Sherman 11/7/2011
	 * @return array a single row as an array from the executed query
	 * @throws Wri_Db_Exception
 	 * @uses $_prepared
 	 */ 
	function fetch($style = PDO::FETCH_ASSOC){
        try {
           return $this->_prepared->fetch($style);
        } catch (PDOException $e) {
            # unable to get a result
            $ar = array(
            	'mysqlErrorCode' => $e->getCode(), 
				'mysqlErrorMsg' => $e->getMessage()
			);
			throw new Exception("Unable to fetch a result row!",1008);
        }
 	}
}