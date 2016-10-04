<?php
class Setting {
	
	private $siteName;
	private $pageTitle;
	private $sections;
	private $subSection;
	private $messages;
	private $canonicalURL;

	private $requestOptions;
	private $requestParameters;
	
	// Page Parameters
	public function setParam($param,$value) {
		
		if($param=="requestOptions") {
			// Set Available Request Parameters For The Page
			if(!is_array($value)) $this->requestOptions = explode(",",$value);
			else $this->requestOptions = $value;
		} else if($param=="requestParameters") {
			// Set Values for The Request Parameters Of The Page
			if(isset($this->requestOptions)) {
				if(is_array($value)) {
					//var_dump($this->requestOptions);
					foreach($value as $k=>$v) {
						//echo "k".$k." v".$v;
						if(in_array($k,$this->requestOptions)) {
							if(!isset($this->requestParameters)) $this->requestParameters = array();
							$this->requestParameters[$k] = $v;
							//echo $v;
						}
					}
				} else {
					throw new ExceptionLogger("Request parameter values must be passed as an array.");
				}
			} else {
				throw new ExceptionLogger("Request parameter options is not set yet.");
			}
		} else {
			// Set Any Other Value As It Comes
			$this->$param = $value;
		}
		return true;
	}
	public function getParam($param) {
		if(isset($this->$param)) return $this->$param;
		else return false;
	}
	public function getRequestParameter($param) {
		if(isset($this->requestParameters[$param])) {
			return $this->requestParameters[$param];
		} else {
			return false;
		}
	}
	public function getRequestParameters($format=null,$except=null) {
		
		if(isset($this->requestParameters)) {
			$temp = $this->requestParameters;
			if($except!=null) unset($temp[$excerpt]);
			if($format==null || $format=="array") return $temp;
			else if($format=="url") return http_build_query($temp);
			else return null;
		} else if(isset($_GET) && count($_GET)>0 && isset($this->requestOptions)) {
			//var_dump($this->requestOptions);
			$temp = array();
			foreach($_GET as $key=>$value) {
				if(in_array($key,$this->requestOptions))
					$temp[$key]=$value;
			}
			if($except!=null) unset($temp[$excerpt]);
			if($format==null || $format=="array") return $temp;
			else if($format=="url") return http_build_query($temp);
			else return null;
		} else {
			return null;
		}
	}
	
	// Sections
	public function setSection($name,$level=0) {
		if(!isset($this->sections)) $this->sections = array();
		$this->sections[$level] = $name;
	}
	public function getSection($level=0) {
		if(!isset($this->sections)) return null;
		return $this->sections[$level];
	}
	public function isSection($comparison,$level=0) {
		if(!isset($this->sections) && $comparison==null) return true;
		else if(!isset($this->sections)) return false;
		else if( strtolower($this->sections[$level]) == strtolower($comparison) ) return true;
		else return false;
	}
	
	// Messages
	public function setMessage($text,$type) {
		return true;
	}
	public function getMessages() {
		return null;
	}

	public function getMeta() {
		$meta = '';

		if(isset($this->canonicalURL))
			$meta.= '<link rel="canonical" href="'.$this->canonicalURL.'" />';

		return $meta;

	}

	public function setSecureConnectionOnly() {
		//var_dump($_SERVER['SERVER_PROTOCOL']); exit();
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
			return true;
		} else {
			header("Location: ".SECUREURL.ltrim($_SERVER["REQUEST_URI"], '/'));
			exit();
		}
	}
	
	
}
?>