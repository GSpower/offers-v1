<?php

class Offer {
	const DB_USER = 'QYKSonic_offers';
	const DB_USER_PASSWD = 'Js2mLLAwEa';

	// const DB_USER = 'root';
	// const DB_USER_PASSWD = 'afrahaza';

	const DB_NAME = 'QYKSonic_offers';
	const DB_HOST = 'localhost';
	const DB_PORT = 3306;

	private $db;
	private $data;
	private $response;
	private $error = false;
	private $errorEmail = false;
	private $errorPhone = false;

	function __construct($data) {
		$this->data = $data;
		$this->init();
	}

	function run() {
		$this->check();
		$this->response();
	}

	private function init() {
		try {
			$this->db = new PDO('mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME, self::DB_USER, self::DB_USER_PASSWD, [ PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' ]);
			$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (Exception $e) {
			$this->response = [ 'error' => -3, 'msg' => $e->getMessage() ];
			$this->error = true;
		}
	}

	private function check() {
		if ($this->error) {
			return;
		}
		if (empty($this->data['email']) || !$this->validateEmail($this->data['email'])) {
			$this->response = [ 'error' => -1, 'msg' => 'email' ];
			$this->errorEmail = true;
		}

		if (empty($this->data['phone'])) {
			$this->response = [ 'error' => -2, 'msg' => 'phone' ];
			$this->errorPhone = true;
		}
		if ($this->errorPhone && $this->errorEmail) {
			$this->error = true;
		}

		if (!$this->error) {
			$this->save($this->data['email'], $this->data['phone'], $_SERVER['REMOTE_ADDR']);
		}

	}

	private function save($email, $phone, $ip) {
		try {
			$sql = "INSERT INTO offers (created, ip, email, phone) VALUES (NOW(), :ip, :email, :phone)";
			$result = $this->db->prepare($sql);
			$result->bindParam(':ip', $ip);
			$result->bindParam(':email', $email);
			$result->bindParam(':phone', $phone);
			$result->execute();
			$this->response = [ 'success' => 1 ];
		} catch (Exception $e) {
			$this->response = [ 'error' => -4, 'msg' => $e->getMessage() ];
			$this->error = true;
		}

	}

	private function response() {
		header('Content-type:application/json;charset=utf-8');
		echo json_encode($this->response);
	}

	private function validateEmail ($email) {
		return preg_match('~[\-_0-9a-z]+@[\-_\.a-z0-9]+\.[a-z]{2,4}~i', $email);
	}

}

$Offer = new Offer($_POST);
$Offer->run();
