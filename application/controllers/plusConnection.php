<?php
class plusConnectionX extends mysqli {
	private $DB_HOST 		= "localhost";
	private $DB_DATABASE 	= "sentralsistem";
	private $DB_USER 		= "sentralsistem";
	private $DB_PASSWORD 	= "$$sentral$$99oo";

	protected $conN;

	public function __construct() {
		$this->conN = mysqli_connect($this->DB_HOST, $this->DB_USER, $this->DB_PASSWORD);
		if(!$this->conN) {
			echo "Connection failed!<br>";
		}
	}

	public function connect() {
		if(!mysqli_select_db($this->conN, $this->DB_DATABASE)) {
			die("Cannot connect database..<br>");
		}

		return $this->conN;
	}
}

?>
