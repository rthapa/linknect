<?php
class Html{

	public $faviconUrl = '';
	public $meta = array(); 
	public $js = array();
	public $css = array();
	public $title = "";

	function __construct(){
		$this->js[] = '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>';
		$this->js[] = '<script src="jquery.cookie.js"></script>';
		$this->js[] = '<script src="js/list.js"></script>';
		$this->js[] = '<script src="js/dash.js"></script>';
		$this->js[] = '<script src="js/nanoscroller.js"></script>';
		$this->css[] = '<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">';
	}

	public function injectHeader(){
		$output = '<!DOCTYPE html>
				<html>
				<head>
					<meta charset="UTF-8">
					<meta name="viewport" content="width=device-width , initial-scale=1">
					<title>'.$this->getTitle().'</title>';

		//meta tag
		foreach($this->meta as $m){
			$output .= $m;
		}

		//favicon
		if(trim($this->faviconUrl) != ''){
			$output .= '<link rel="shortcut icon" href="'.$this->faviconUrl.'">';
		}

		//js
		foreach($this->js as $j){
			$output .= $j;
		}

		//css
		foreach($this->css as $c){
			$output .= $c;
		}

					
		$output .= '</head>
					<body>';

		return $output;
	}

	public function injectFooter(){
		$output = '</body>
					</html>';
		return $output;
	}

	public function setTitle($title){
		$this->title = $title;
	}

	public function getTitle(){
		return $this->title;
	}
}
