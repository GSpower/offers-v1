<?php

class databaseDownload {
	const SERVERNAME = 'localhost';
	const USERNAME = 'QYKSonic_offers';
	const PASSWORD = 'Js2mLLAwEa';
	const DATABASE = 'QYKSonic_offers';
	private $hashPassword = '$2y$10$.Q/bfzZiibLa7WZvKbOojOMRbPOq1VODNVZFst4Tw3sXdC28tUYaS';
	private $password;
	private $db;

	function __construct($password) {
		$this->password = $password;
		$this->db = new PDO("mysql:host=" . self::SERVERNAME . ";dbname=" . self::DATABASE, self::USERNAME, self::PASSWORD);
	}

  private function checkPassword() {
  	return password_verify($this->password, $this->hashPassword);
  }

  private function download() {
		try {
		 	$stmt = $this->db->prepare('
        	SELECT email, phone 
        	FROM offers');
    	$stmt->execute();

	    $filename = 'specialoffers_' . date('Y.m.d') . '.csv';
	    $data = fopen('php://output', 'w');

	    if ($data) {
				header('Content-Type: text/csv');
		    header('Content-Disposition: attachment; filename=' . $filename);
		    header('Pragma: no-cache');
		    header('Expires: 0');
		    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		    	fputcsv($data, $row);
		    }
	    } 
	    fclose($data);

		} catch (Exception $e) {
			error_log($e->getMessage());
		}
  }
  public function run() {  	
  	if ($this->checkPassword()) {
  		$this->download();
  		echo 'Fajl uspesno skinut';
  	} else {
  		echo 'Pogresna sifra';
  	}
  }
}
if (isset($_POST['submit'])) {
	$databaseDownload = new databaseDownload($_POST['password']);
	$databaseDownload->run();
}

?>
<form action="database_export.php" method="post"> 
    Password:<br> 
    <input type="password" name="password"><br><br> 
    <input type="submit" name="submit" value="Download"> 
</form> 



