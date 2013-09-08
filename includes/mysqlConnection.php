<?
	class mysqlConnection {
		private $dbHostname;
		private $dbUsername;
		private $dbPassword;
		private $dbName;
		private $dbLink;
		
		private $result;
		private $storedResults = array();
		private $storedQueries = array();
		
		private $table;
		private $where;
		private $selectCols;
		private $joins;
		private $order;
		private $limit;
		private $group;
		private $inserts;
		private $updates;
		private $rows = array();
		
		
		function __construct($dbHostname = '', $dbUsername = '', $dbPassword = '') {
			$this->dbHostname = $dbHostname;
			$this->dbUsername = $dbUsername;
			$this->dbPassword = $dbPassword;
		}
		
		public function connect() {
			$this->dbLink = mysql_connect($this->dbHostname, $this->dbUsername, $this->dbPassword) or die('Please contact the administrator and report error 101.');
			
			return TRUE;
		}
		
		public function disconnect() {
			return mysql_close($this->dbLink);
		}
		
		public function selectDB($dbName) {
			$this->dbName = $dbName;
			mysql_select_db($this->dbName) or die('Unable to select database '.$this->dbName);
			
			return TRUE;
		}
		
		
		public function setTable() {
			$this->table = '';

			foreach (func_get_args() as $table) $this->table .= $table.', ';
			$this->table = substr($this->table, 0, -2);
			
			return TRUE;
		}

		public function setWhere() {
			$this->where = ' WHERE';
			foreach (func_get_args() as $where) {
				$this->where .= ' '.$where;
			}
			
			return TRUE;
		}
		
		public function setSelectCols() {
			$this->selectCols = '';
			foreach (func_get_args() as $value) {
				$value = explode(' ', $value);
				if (sizeof($value) > 2) {
					$tempVal = explode(' ', $value, -1);
					$tempVal = implode(' ', $tempVal);
					$value = array($tempVal, $value[sizeof($value) - 1]);
				}
				
				if ($this->selectCols != '') $this->selectCols .= ', '.$value[0];
				else $this->selectCols = $value[0];
				
				if (isset($value[1])) $this->selectCols .= ' AS "'.$value[1].'"';
			}
			
			return TRUE;
		}
		
		public function setJoins() {
			$args = func_get_args();
			$this->joins = '';
			for ($argCount = 0; $argCount < sizeof($args); $argCount += 2) {
				if ($args[$argCount] != '') { $this->joins .= ' '.strtoupper($args[$argCount]); }
				$this->joins .= ' JOIN '.$args[$argCount + 1];
			}
		}
		
		public function setOrder() {
			$this->order = '';
			foreach (func_get_args() as $value) {
				if (is_string($value)) $value = explode(' ', $value);

				if ($this->order != '') $this->order .= ', '.$value[0].' '.strtoupper($value[1]);
				else $this->order = ' ORDER BY '.$value[0].' '.strtoupper($value[1]);
			}
			
			return TRUE;
		}

		public function setLimit($start, $num = 0) {
			$this->limit = ' LIMIT '.intval($start);
			if ($num != 0) $this->limit .= ', '.intval($num);
			
			return TRUE;
		}
		
		public function setGroup() {
			foreach (func_get_args() as $value) {
				if ($this->group != '') $this->order .= ', '.$value;
				else $this->group = ' GROUP BY '.$value;
			}
			
			return TRUE;
		}
		
		public function setInserts() {
			$columns = '';
			$values = '';
			
			if (func_num_args() == 1 && is_array(func_get_arg(0))) {
				$inserts = func_get_arg(0);
				foreach ($inserts as $key => $value) {
					$columns .= ', '.$key;
					if (preg_match('/.*\(\)$/', $value)) $values .= ', '.$value;
					else $values .= ', "'.$value.'"';
				}
				
				$columns = substr($columns, 2);
				$values = substr($values, 2);
				$this->inserts = ' ('.$columns.') VALUES ('.$values.')';
			} elseif (func_num_args() > 1) {
			}
			
			return TRUE;
		}
		
		public function setUpdates($updates) {
			$this->updates = '';
			foreach ($updates as $key => $value) $this->updates .= ', '.$key.' = "'.$value.'"';
			
			if ($this->updates[0] == ',') $this->updates = substr($this->updates, 1);
			$this->updates = ' SET'.$this->updates;
			
			return TRUE;
		}
		
		public function getValue($value) {
			return $this->$value;
		}
		
		public function setupInserts() {
			$columns = '';
			$values = '';
			
			$args = func_get_args();
			if (func_num_args() == 1 && !is_array(current($args[0]))) {
				$inserts = func_get_arg(0);
				foreach ($inserts as $key => $value) {
					$columns .= ', `'.$key.'`';
					if (is_numeric($value)) $values .= ", $value";
					else $values .= ', "'.$value.'"';
				}
				
				$columns = substr($columns, 2);
				$values = substr($values, 2);
				$insertStr = "({$columns}) VALUES ({$values})";
			} elseif (func_num_args() == 1 && is_array(current($args[0]))) {
				$args = $args[0];
				$insertStr = '(';
				$first = TRUE;
				foreach ($args as $inserts) {
					if ($first) {
						$values = '';
						foreach ($inserts as $key => $value) {
							$insertStr .= "`$key`, ";
							if (is_numeric($value)) $values .= "$value, ";
							else $values .= '"'.$value.'", ';
						}
						$insertStr = substr($insertStr, 0, -2).') VALUES ('.substr($values, 0, -2).'), ';
						$first = FALSE;
					} else {
						$values = '';
						foreach ($inserts as $value) {
							if (is_numeric($value)) $values .= "$value, ";
							else $values .= '"'.$value.'", ';
						}
						$insertStr .= '('.substr($values, 0, -2).'), ';
					}
				}
				
				$insertStr = substr($insertStr, 0, -2);
			} elseif (func_num_args() > 1) {
				$insertStr = '(';
				$first = TRUE;
				foreach ($args as $inserts) {
					if ($first) {
						foreach ($inserts as $value) $insertStr .= "`$value`, ";
						$insertStr = substr($insertStr, 0, -2).') VALUES ';
						$first = FALSE;
					} else {
						$values = '';
						foreach ($inserts as $value) {
							if (is_numeric($value)) $values .= "$value, ";
							else $values .= '"'.$value.'", ';
						}
						$insertStr .= '('.substr($values, 0, -2).'), ';
					}
				}
				
				$insertStr = substr($insertStr, 0, -2);
			}
			
			return $insertStr;
		}
		
		public function setupUpdates($updates) {
			$updateString = '';
			foreach ($updates as $key => $value) {
				if (is_numeric($value)) $updateString .= ', `'.$key.'` = '.$value;
				else $updateString .= ', `'.$key.'` = "'.$value.'"';
			}
			
			if ($updateString[0] == ',') $updateString = substr($updateString, 2);
			
			return $updateString;
		}
		
		public function clearAttributes() {
			if (!func_num_args()) { unset($this->result, $this->storedResults, $this->table, $this->joins, $this->selectCols, $this->where, $this->order, $this->limit, $this->inserts, $this->updates); }
			else { for ($count = 0; $count < func_num_args(); $count++) {
					$var = func_get_arg($count);
					unset($this->$var);
			} }
		}
		
		public function clearStoredResults() {
			unset($this->storedResults);
		}
		
		public function stdQuery($type) {
			$attributes = array();
			for ($count = 1; $count < func_num_args(); $count++) {
				$argument = func_get_arg($count);
				array_push($attributes, strtolower($argument));
			}
			
			if (strtolower($type) == 'select') {
				$query = 'SELECT ';
				if (in_array('selectcols', $attributes)) $query .= $this->selectCols;
				else $query .= '*';
				$query .= ' FROM '.$this->table;
				if (in_array('join', $attributes)) $query .= $this->joins;
				if (in_array('where', $attributes)) $query .= $this->where;
				if (in_array('group', $attributes)) $query .= $this->group;
				if (in_array('order', $attributes)) $query .= $this->order;
				if (in_array('limit', $attributes)) $query .= $this->limit;
			} elseif (strtolower($type) == 'insert') {
				$query = 'INSERT INTO '.$this->table;
				$query .= $this->inserts;
			} elseif (strtolower($type) == 'update') {
				$query = 'UPDATE '.$this->table;
				$query .= $this->updates;
				if (in_array('where', $attributes)) $query .= $this->where;
			} elseif (strtolower($type) == 'delete') {
				$query = 'DELETE FROM '.$this->table;
				$query .= $this->where;
				if (in_array('limit', $attributes)) $query .= $this->limit;
			}
			
			if (in_array('dispquery', $attributes)) echo $query;
			
			$this->result = mysql_query($query);
			if ($this->result) return TRUE;
			else return FALSE;
		}
		
		public function query($query, $label = '') {
			$this->result = mysql_query($query);
			
			if ($label != '') $this->storedResults[$label] = $this->result;
			
			if ($this->result) return TRUE;
			else return FALSE;
		}
		
		public function customQuery($query) {
			$this->result = mysql_query($query);
			
			if ($this->result) return TRUE;
			else return FALSE;
		}
		
		public function lastInsertID() {
			list($lastID) = mysql_fetch_array(mysql_query('SELECT LAST_INSERT_ID()'));
			
			return $lastID;
		}
		
		public function getResult() {
			return $this->result;
		}
		
		public function storeResult($label) {
			$this->storedResults[$label] = $this->result;
			
			return TRUE;
		}
		
		public function getStoredResult($label) {
			return $this->storedResults[$label];
		}
		
		public function setStoredResult($label) {
			$this->result = $this->storedResults[$label];
			
			return TRUE;
		}
		
		public function getRow($label = '') {
			if ($this->storedResults[$label] && $label != '') return mysql_fetch_array($this->storedResults[$label]);
			elseif ($this->result) return mysql_fetch_array($this->result);
			else {
				echo $label == ''?mysql_error($this->result):mysql_error($this->storedResults[$label]);
				return 0;
			}
		}
		
		public function getList($label = '') {
			if ($this->storedResults[$label] && $label != '') return mysql_fetch_array($this->storedResults[$label]);
			elseif ($this->result) return mysql_fetch_array($this->result);
			else {
				echo $label == ''?mysql_error($this->result):mysql_error($this->storedResults[$label]);
				return 0;
			}
		}
		
		public function numRows($label = '') {
			if ($label == '') return mysql_num_rows($this->result);
			else return mysql_num_rows($this->storedResults[$label]);
		}
		
		public function affectedRows($label = '') {
			return mysql_affected_rows($this->dbLink);
		}
		
		public function resetResult($label = '') {
			if ($label == '' && $this->numRows()) mysql_data_seek($this->result, 0);
			elseif ($label != '' && $this->numRows($label)) mysql_data_seek($this->storedResults[$label], 0);
			
			return TRUE;
		}
		
		public function getRows($label = '', $row = 0) {
			$this->rows = array();
			$this->resetResult($label);
			
			if ($label == '') $result = $this->result;
			else $result = $this->storedResults[$label];
			
			while ($info = mysql_fetch_array($result)) {
				array_push($this->rows, $info);
			}
			
/*			if ($this->numRows($label) == 1 && $this->rows[0]) { return $this->rows[0]; }
			else*/if ($row > 0) return $this->rows[$row - 1];
			else return $this->rows;
		}
	}
?>