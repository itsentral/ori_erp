<?php

	class database_ORI_TANKI extends mysqli {
		private $DB_HOST 		= 'localhost';
		private $DB_DATABASE 	= 'tanki';
        private $DB_USER 		= 'sentral';
        private $DB_PASSWORD 	= 'Sentral@2024**';



		public function __construct() {
			$this->_conn = mysqli_connect($this->DB_HOST, $this->DB_USER, $this->DB_PASSWORD);

			if(!$this->_conn) {
				echo 'Connection failed!<br>';
			}
		}

		public function connect() {
			if(!mysqli_select_db($this->_conn, $this->DB_DATABASE)) {
				die("Cannot connect database..<br>");
			}

			return $this->_conn;

		}

	}



?>
