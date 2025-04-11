<?php

	class database_ori extends mysqli {
		private $DB_HOST 		= 'localhost';
		private $DB_DATABASE 	= 'ori_dummy';
        private $DB_USER 		= 'root';
        private $DB_PASSWORD 	= 'sentral2022**';
		
		protected $_conn;

		public function __construct() {
			$this->_conn = mysqli_connect($this->DB_HOST, $this->DB_USER, $this->DB_PASSWORD);
			echo "masuk<pre>";print_r($this-_conn);exit; 
			if(!$this->_conn) {
				echo 'Connection failed!<br>';
			}
		}

		public function connect() {
			if(!mysqli_select_db($this->_conn, $this->DB_DATABASE)) {
				die("Cannot connect database..<br>");
			}

			//return $this->_conn;
			return 'gagal bro';
		}
	}
	
	
	
?>