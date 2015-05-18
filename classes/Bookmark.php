<?php
/**
 * Bookmark class for linknect.com
 * @author Rabi Thapa
 * @version  1.1 2014
 */
class Bookmark{
	private $bookmark_id;
	private $bookmark_original_title;
	private $bookmark_title;
	private $bookmark_link;
	private $bookmark_description;
	private $bookmark_icon;
	private $bookmark_owner;
	private $bookmark_date;
	private $bookmark_souce;
	private $bookmark_thumbnail;

	public static function getFromId($id, $connection){
		$query = $connection->query('SELECT * FROM bookmarks
							WHERE bookmark_id =:id LIMIT 1',
							array(":id"=>$id)) ;
		if(count($query) > 0){
			$obj = new Bookmark();
			foreach($query = $query[0] as $key => $value){
				$obj->$key = $value;
			}
			return $obj;
		}else{
			return new Bookmark;
		}
	}

	public static function getFromSql($sql, $pdoParams = array(), $connection){
		$query = $connection->query($sql, $pdoParams);

		$objArr = array();
		if(count($query)>0){
			foreach($query as $key => $value){
				$obj = new Bookmark();
				foreach($query[$key] as $k => $v){
					$obj->$k = $v;
				}
				$objArr[] = $obj;
			}

		}
		return $objArr;
	}

	public static function getTotalBookmarksFromUserId($id, $connection){
		$totalBookmarks = $connection->query('SELECT * FROM bookmarks
												WHERE bookmark_owner = :id',
												array(":id"=>$id)) ;
		return count($totalBookmarks);
	}

	public function save($connection){
		if($this->getId()){
			$connection->updateRow('UPDATE 
										bookmarks
									SET 
										bookmark_link = :blink,
										bookmark_icon = :icon,
										bookmark_source = :source,
										bookmark_thumbnail = :thumbnail,
										bookmark_original_title = :btitle,
										bookmark_description = :bdesc,
										bookmark_date = now()
									WHERE
										bookmark_id = :bid',
									array(':btitle' => $this->getOriginalTitle(),
										':icon'=>$this->getIcon(),
										':source'=>$this->getSource(),
										':thumbnail'=>$this->getThumbnail(),
										':bdesc' => $this->getDescription(),
										':blink' => $this->getLink(),
										':bid' => $this->getId(),
										));
		}else{
			$connection->insertRow('INSERT INTO 
										bookmarks(
											bookmark_original_title,
											bookmark_link,
											bookmark_source,
											bookmark_icon,
											bookmark_thumbnail,
											bookmark_description,
											bookmark_owner, 
											bookmark_date
										) 
									values
										(
											:origTitle,
											:link, 
											:source,
											:icon,
											:thumbnail,
											:description, 
											:addedby, 
											now()
										)',
									 array(':origTitle' =>$this->getOriginalTitle(),
									 		':link'=>$this->getLink(),
									 		':source'=>$this->getSource(),
									 		':icon'=>$this->getIcon(),
									 		':thumbnail'=>$this->getThumbnail(),
									 		':description'=>$this->getDescription(),
									 		':addedby'=>$this->getOwner()
									 		));
			
			$this->bookmark_id = $connection->getLastInsertId();
		}
	}

	/* Setters */
	public function setOriginalTitle($value){
		return $this->bookmark_original_title = $value;
	}

	public function setTitle($value){
		return $this->bookmark_title = $value;
	}

	public function setLink($value){
		return $this->bookmark_link = $value;
	}

	public function setDescription($value){
		return $this->bookmark_description = $value;
	}

	public function setIcon($value){
		return $this->bookmark_icon = $value;
	}

	public function setOwner($value){
		return $this->bookmark_owner = $value;
	}

	public function setDate($value){
		return $this->bookmark_date = $value;
	}

	public function setSource($value){
		return $this->bookmark_source = $value;
	}

	public function setThumbnail($value){
		return $this->bookmark_thumbnail = $value;
	}

	/* Getters */
	public function getId(){
		return $this->bookmark_id;
	}

	public function getOriginalTitle(){
		return $this->bookmark_original_title;
	}

	public function getTitle(){
		return $this->bookmark_title;
	}

	public function getLink(){
		return $this->bookmark_link;
	}

	public function getDescription(){
		return $this->bookmark_description;
	}

	public function getIcon(){
		return $this->bookmark_icon;
	}

	public function getOwner(){
		return $this->bookmark_owner;
	}

	public function getDate(){
		return $this->bookmark_date;
	}

	public function getSource(){
		return $this->bookmark_source;
	}

	public function getThumbnail(){
		return $this->bookmark_thumbnail;
	}
}
