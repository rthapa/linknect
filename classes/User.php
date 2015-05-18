<?php
/**
 * User class for linknect.com
 * @author Rabi Thapa
 * @version  1.1 2014
 */
class User{
	private $id;
	private $email;
	private $pwd;
	private $ip;
	private $lastlogin;
	private $username;
	private $activated;
	private $name;

	public static function getFromAuth($emailOrUsername, $password, $connection ){
		$auth = $connection->query('SELECT * FROM users WHERE (username = :eOu AND pwd = :pwd) OR (email = :eOu AND pwd = :pwd)',
									array(':eOu' => $emailOrUsername,
											':pwd' => md5($password),
											));

		if(count($auth) > 0){
			$obj = new User();
			foreach($auth = $auth[0] as $key => $value){
				$obj->$key = $value;
			}
			return $obj;
		}else{
			return false;
		}
	}

	public static function getUsernameFromId($id, $connection){
		$usernameQuery = $connection->query('SELECT * FROM users
												WHERE id = :id',
												array(":id"=>$id)) ;
		$username = "";
		foreach($usernameQuery as $user){
			$username = $user['username'];
		}
		return $username;
	}

	public static function emailExist($email, $connection){
		$query = $connection->query("SELECT * FROM users WHERE email = :email LIMIT 1",
							array(":email"=>$email));
		if(count($query) < 1){
			return false;
		}else{
			return true;
		}
	}

	public static function usernameExist($username, $connection){
		$query = $connection->query("SELECT * FROM users WHERE username = :username LIMIT 1",
							array(":username"=>$username));
		if(count($query) < 1){
			return false;
		}else{
			return true;
		}
	}

	public static function getFromId($id, $connection){
		$query = $connection->query('SELECT * FROM users
							WHERE id =:id LIMIT 1',
							array(":id"=>$id)) ;
		if(count($query) > 0){
			$obj = new User();
			foreach($query = $query[0] as $key => $value){
				$obj->$key = $value;
			}
			return $obj;
		}else{
			return new User;
		}
	}

	public static function getFromSql($sql, $pdoParams = array(), $connection){
		$query = $connection->query($sql, $pdoParams);

		$objArr = array();
		if(count($query)>0){
			foreach($query as $key => $value){
				$obj = new User();
				foreach($query[$key] as $k => $v){
					$obj->$k = $v;
				}
				$objArr[] = $obj;
			}
		}
		return $objArr;
	}

	// public static function getFromEmailOrUsername($emailOrUsername, $connection){
	// 	$query = $connection->query('SELECT * FROM users
	// 						WHERE email =:id LIMIT 1',
	// 						array(":id"=>$id)) ;
	// 	if(count($query) > 0){
	// 		$obj = new User();
	// 		foreach($query = $query[0] as $key => $value){
	// 			$obj->$key = $value;
	// 		}
	// 		return $obj;
	// 	}else{
	// 		return new User;
	// 	}
	// }

	public function save($connection){
		if($this->getId()){
			$connection->updateRow('UPDATE 
										users
									SET 
										email = :email,
										pwd = :pwd,
										name = :name,
										ip = :ip,
										lastlogin = now(),
										username = :username,
										activated = :activated
									WHERE
										id = :id',
									array(':email' => $this->getEmail(),
										':pwd' => $this->getPassword(),
										':name' => $this->getName(),
										':ip' => preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR')),
										':username' => $this->getUsername(),
										':activated' => $this->getActivated(),
										':id' => $this->getId()
										));
		}else{
			$connection->insertRow('INSERT INTO
										users(
											email,
											pwd,
											name, 
											ip,
											lastlogin,
											username,
											activated
										) 
									values
										(
											:email,
											:pwd, 
											:name, 
											:ip, 
											now(),
											:username,
											:activated
										)',
									 array(':email' => $this->getEmail(),
									 		':pwd' => $this->getPassword(),
									 		':name' => $this->getName(),
									 		':ip' => preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR')),
									 		':username' => $this->getUsername(),
									 		':activated' => $this->getActivated()
									 		));

			$this->id = $connection->getLastInsertId();
		}
	}

	/* Setters */
	public function setEmail($value){
		return $this->email = $value;
	}

	public function setPassword($value){
		return $this->pwd = $value;
	}

	public function setName($value){
		return $this->name = $value;
	}

	public function setIp($value){
		return $this->ip = $value;
	}

	public function setLastLogin($value){
		return $this->lastlogin = $value;
	}

	public function setUsername($value){
		return $this->username = $value;
	}

	public function setActivated($value){
		return $this->activated = $value;
	}

	/* Getters */
	public function getId(){
		return $this->id;
	}

	public function getEmail(){
		return $this->email;
	}

	public function getPassword(){
		return $this->pwd;
	}

	public function getName(){
		return $this->name;
	}

	public function getIp(){
		return $this->ip;
	}

	public function getLastLogin(){
		return $this->lastlogin;
	}

	public function getUsername(){
		return $this->username;
	}

	public function getActivated(){
		return $this->activated;
	}
}
