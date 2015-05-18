<?php
/**
 * BookmarkList class for linknect.com
 * @author Rabi Thapa
 * @version  1.1 2014
 */
class BookmarkList{

	private $id;
	private $list_name;
	private $list_description;
	private $list_owner;
	private $list_privacy;
	private $list_datetime;
	private $list_color;
	private $list_type;

	public static function getListNameFromId($id, $connection){
		$query = $connection->query('SELECT list_name FROM list
								WHERE list.id =:id LIMIT 1',
								array(":id"=>$id)) ;
		$list_name="";
		foreach($query as $q){
			$list_name = $q['list_name'];
		}
		return $list_name;
	}

	public static function getListOwnerFromId($id, $connection){
		$query = $connection->query('SELECT * FROM list
								WHERE list.id =:id LIMIT 1',
								array(":id"=>$id)) ;
		$list_owner="";
		foreach($query as $q){
			$list_owner = $q['list_owner'];
		}
		return $list_owner;
	}

	public static function getTotalBookmarksFromListId($id, $connection){
		$totalBookmarks = $connection->query('SELECT * FROM bookmarks
												INNER JOIN link ON bookmarks.bookmark_id = link.bookmarkid
												WHERE link.listid = :id',
												array(":id"=>$id)) ;
		return count($totalBookmarks);
	}

	public static function getTotalListFromUserId($id, $connection){
		$totalBookmarks = $connection->query('SELECT * FROM list
												WHERE list_owner = :id',
												array(":id"=>$id)) ;
		return count($totalBookmarks);
	}

	public static function getFromId($id, $connection){
		$query = $connection->query('SELECT * FROM list
							WHERE id =:id LIMIT 1',
							array(":id"=>$id)) ;
		if(count($query) > 0){
			$obj = new BookmarkList();
			foreach($query = $query[0] as $key => $value){
				$obj->$key = $value;
			}
			return $obj;
		}else{
			return new BookmarkList;
		}
	}

	public static function getFromSql($sql, $pdoParams = array(), $connection){
		$query = $connection->query($sql, $pdoParams);

		$objArr = array();
		if(count($query)>0){
			foreach($query as $key => $value){
				$obj = new BookmarkList();
				foreach($query[$key] as $k => $v){
					$obj->$k = $v;
				}
				$objArr[] = $obj;
			}
		}
		return $objArr;
	}

	public static function getPrivacyIcon($type){
		switch($type){
			case 'Open':
				return 'fa-unlock';
				break;
			case 'Private':
				return 'fa-lock';
				break;
			case 'Unlisted':
				return 'fa-eye-slash';
				break;
		}
	}

	public function save($connection){
		if($this->getId()){
			$connection->updateRow('UPDATE 
										list
									SET 
										list_name = :name,
										list_description = :description,
										list_owner = :owner,
										list_privacy = :privacy,
										list_datetime = now(),
										list_type = :type
									WHERE
										id = :id',
									array(':name' => $this->getName(),
										':description' => $this->getDescription(),
										':owner' => $this->getOwner(),
										':privacy' => $this->getPrivacy(),
										':id' => $this->getId(),
										':type' => $this->getType(),
										));
		}else{
			$connection->insertRow('INSERT INTO
										list(
											list_name,
											list_description,
											list_owner,
											list_privacy, 
											list_datetime,
											list_type
										) 
									values
										(
											:name,
											:description, 
											:owner, 
											:privacy, 
											now(),
											:type
										)',
									 array(':name' => $this->getName(),
									 		':description' => $this->getDescription(),
									 		':owner' => $this->getOwner(),
									 		':privacy' => $this->getPrivacy(),
									 		// ':color' => $this->getColor(),
									 		':type' => $this->getType()
									 		));

			$this->id = $connection->getLastInsertId();
		}
	}

	/* Setters */
	public function setName($value){
		return $this->list_name = $value;
	}

	public function setDescription($value){
		return $this->list_description = $value;
	}

	public function setOwner($value){
		return $this->list_owner = $value;
	}

	public function setPrivacy($value){
		return $this->list_privacy = $value;
	}

	public function setDateTime($value){
		return $this->list_privacy = $value;
	}

	public function setColor($value){
		return $this->list_color = $value;
	}

	public function setType($value){
		return $this->list_type = $value;
	}

	/* Getters */
	public function getId(){
		return $this->id;
	}

	public function getName(){
		return $this->list_name;
	}

	public function getDescription(){
		return $this->list_description;
	}

	public function getOwner(){
		return $this->list_owner;
	}

	public function getPrivacy(){
		return $this->list_privacy;
	}

	public function getDateTime(){
		return $this->list_datetime;
	}

	public function getColor(){
		return $this->list_color;
	}

	public function getType(){
		return $this->list_type;
	}
}
