<?php
/**
 * UserGroup class for linknect.com
 * @author Rabi Thapa
 * @version  1.1 2014
 */
class UserGroup{
	private $id;
	private $userid;
	private $listid;
	private $eventdate;

	public static function getFromId($id, $connection){
		$query = $connection->query('SELECT * FROM usergroup
							WHERE id =:id LIMIT 1',
							array(":id"=>$id)) ;
		if(count($query) > 0){
			$obj = new UserGroup();
			foreach($query = $query[0] as $key => $value){
				$obj->$key = $value;
			}
			return $obj;
		}else{
			return new UserGroup;
		}
	}
	
	public static function getFromSql($sql, $pdoParams = array(), $connection){
		$query = $connection->query($sql, $pdoParams);

		$objArr = array();
		if(count($query)>0){
			foreach($query as $key => $value){
				$obj = new UserGroup();
				foreach($query[$key] as $k => $v){
					$obj->$k = $v;
				}
				$objArr[] = $obj;
			}

		}
		return $objArr;
	}

	public function save($connection){
		if($this->getId()){
			$connection->updateRow('UPDATE 
										UserGroup
									SET 
										list = :listid,
										userid = :userid,
										eventdate = now()
									WHERE
										id = :id',
									array(
										':listid' => $this->getListId(),
										':userid' => $this->getUserId(),
										':id' => $this->getId(),
										));
		}else{
			$connection->insertRow('INSERT INTO 
										UserGroup(
											listid,
											userid,
											eventdate
										) 
									values
										(
											:listid,
											:userid, 
											now()
										)',
									 array(':listid' =>$this->getListId(),
									 		':userid'=>$this->getUserId()
									 		));
			
			$this->id = $connection->getLastInsertId();
		}
	}

	/* Setters */
	public function setUserId($value){
		return $this->userid = $value;
	}

	public function setListId($value){
		return $this->listid = $value;
	}

	public function setDate($value){
		return $this->eventdate = $value;
	}

	/* Getters */
	public function getId(){
		return $this->id;
	}

	public function getUserId(){
		return $this->userid;
	}

	public function getListId(){
		return $this->listid;
	}

	public function getDate(){
		return $this->eventdate;
	}
}
