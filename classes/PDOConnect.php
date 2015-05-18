<?php
/**
 * Author: rabi thapa
 * Version: 1.0
 * //http://codereview.stackexchange.com/questions/29362/very-simple-php-pdo-class
 */
class PDOConnect{

	private $pdo;

	private $dbConnected = false;

	public function __construct(){
		$this->connect();
	}

	private function connect(){
		try{
			$this->pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_DATABASE, DB_USER, DB_PASSWORD);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnected = true;
		}catch(PDOException $e){
			echo $e->getMessage();
			die();
		}
	}

	public function disconnect(){
		$this->pdo = null;
		$this->dbConnected = false;
	}

	public function getLastInsertId(){
		return $this->pdo->lastInsertId();
	}
	//$query->fetch(), fetchAll()
	public function query($query, $params = null){

		// $pdoquery = $this->pdo->prepare($query);

		// $pdoquery->execute($params);

		// return $pdoquery->setFetchMode(PDO::FETCH_ASSOC);
		try{
			$stmt = $this->pdo->prepare($query);
			$stmt->execute($params);
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			throw new Exception($e->getMessage());
		}
	}

	public function insertRow($query, $params){
		try{ 
			$stmt = $this->pdo->prepare($query); 
			$stmt->execute($params);
		}catch(PDOException $e){
			throw new Exception($e->getMessage());
		}
	}
	public function updateRow($query, $params){
		return $this->insertRow($query, $params);
	}

	public function deleteRow($query, $params){
		return $this->insertRow($query, $params);
	}

	public function getPdo(){
		return $this->pdo;
	}

}
?>
