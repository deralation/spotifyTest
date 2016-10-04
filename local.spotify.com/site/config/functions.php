<?php
/* MachineName */
function getAlias($text, $options = "dashes") {
	$text = trim($text);
	$search = array('Ç', 'ç', 'Ğ', 'ğ', 'ı', 'İ', 'Ö', 'ö', 'Ş', 'ş', 'Ü', 'ü', ' ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'A', 'A', 'A', 'Ç', 'C', 'C', 'C', 'C', 'D', 'D', 'È', 'É', 'Ê', 'Ë', 'E', 'E', 'E', 'E', 'E', 'G', 'Ğ', 'G', 'G', 'H', 'H', 'Ì', 'Í', 'Î', 'Ï', 'İ', 'Ñ', 'N', 'N', 'N', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'O', 'O', 'O', 'Œ', 'R', 'R', 'R', 'S', 'Ş', 'S', 'Š', 'T', 'T', 'T', 'Ù', 'Ú', 'Û', 'Ü', 'U', 'U', 'U', 'U', 'U', 'U', 'W', 'Y', 'Ÿ', 'Y', 'Z', 'Z', 'Z', 'à', 'á', 'â', 'ã', 'ä', 'a', 'a', 'a', 'å', 'æ', 'ç', 'd', 'd', 'è', 'é', 'ê', 'ë', 'ƒ', 'g', 'ğ', 'g', 'g', 'h', 'h', 'ì', 'í', 'î', 'ï', 'i', 'i', 'i', 'i', 'ı', 'j', 'k', 'l', 'l', 'l', 'l', 'ñ', 'n', 'n', 'n', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'o', 'o', 'o', 'œ', 'r', 'r', 'r', 's', 'š', 'ş', 't', 't', 'ù', 'ú', 'û', 'ü', 'u', 'u', 'u', 'u', 'u', 'u', 'w', 'ÿ', 'y', 'y', 'z', 'z', 'z', 'ß');
	$replace = array('C', 'c', 'G', 'g', 'i', 'I', 'O', 'o', 'S', 's', 'U', 'u', '-', 'A', 'A', 'A', 'A', 'Ae', 'A', 'A', 'A', 'A', 'A', 'C', 'C', 'C', 'C', 'C', 'D', 'D', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'G', 'G', 'G', 'G', 'H', 'H', 'I', 'I', 'I', 'I', 'I', 'N', 'N', 'N', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'OE', 'R', 'R', 'R', 'S', 'S', 'S', 'S', 'T', 'T', 'T', 'U', 'U', 'U', 'Ue', 'U', 'U', 'U', 'U', 'U', 'U', 'W', 'Y', 'Y', 'Y', 'Z', 'Z', 'Z', 'a', 'a', 'a', 'a', 'ae', 'a', 'a', 'a', 'a', 'ae', 'c', 'd', 'd', 'e', 'e', 'e', 'e', 'f', 'g', 'g', 'g', 'g', 'h', 'h', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'j', 'k', 'l', 'l', 'l', 'l', 'n', 'n', 'n', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'oe', 'r', 'r', 'r', 's', 's', 's', 't', 't', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'w', 'y', 'y', 'y', 'z', 'z', 'z', 'ss');
	$text = str_replace($search, $replace, $text);
	$permalink = preg_replace('/[^a-zA-Z0-9-_]/', '', $text);
	if($options == "nodashes") $permalink = mb_strtolower(strtr($permalink, " ", "æ"));
	else $permalink = mb_strtolower(strtr($permalink, " ", "-"));
	$permalink = str_replace("--", "-", $permalink);
	$permalink = str_replace("39", "", $permalink);
	return $permalink;
}

function getFileName($text, $options = "dashes") {
	$text = trim($text);
	$search = array('Ç', 'ç', 'Ğ', 'ğ', 'ı', 'İ', 'Ö', 'ö', 'Ş', 'ş', 'Ü', 'ü', ' ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'A', 'A', 'A', 'Ç', 'C', 'C', 'C', 'C', 'D', 'D', 'È', 'É', 'Ê', 'Ë', 'E', 'E', 'E', 'E', 'E', 'G', 'Ğ', 'G', 'G', 'H', 'H', 'Ì', 'Í', 'Î', 'Ï', 'İ', 'Ñ', 'N', 'N', 'N', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'O', 'O', 'O', 'Œ', 'R', 'R', 'R', 'S', 'Ş', 'S', 'Š', 'T', 'T', 'T', 'Ù', 'Ú', 'Û', 'Ü', 'U', 'U', 'U', 'U', 'U', 'U', 'W', 'Y', 'Ÿ', 'Y', 'Z', 'Z', 'Z', 'à', 'á', 'â', 'ã', 'ä', 'a', 'a', 'a', 'å', 'æ', 'ç', 'd', 'd', 'è', 'é', 'ê', 'ë', 'ƒ', 'g', 'ğ', 'g', 'g', 'h', 'h', 'ì', 'í', 'î', 'ï', 'i', 'i', 'i', 'i', 'ı', 'j', 'k', 'l', 'l', 'l', 'l', 'ñ', 'n', 'n', 'n', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'o', 'o', 'o', 'œ', 'r', 'r', 'r', 's', 'š', 'ş', 't', 't', 'ù', 'ú', 'û', 'ü', 'u', 'u', 'u', 'u', 'u', 'u', 'w', 'ÿ', 'y', 'y', 'z', 'z', 'z', 'ß');
	$replace = array('C', 'c', 'G', 'g', 'i', 'I', 'O', 'o', 'S', 's', 'U', 'u', '-', 'A', 'A', 'A', 'A', 'Ae', 'A', 'A', 'A', 'A', 'A', 'C', 'C', 'C', 'C', 'C', 'D', 'D', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'G', 'G', 'G', 'G', 'H', 'H', 'I', 'I', 'I', 'I', 'I', 'N', 'N', 'N', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'OE', 'R', 'R', 'R', 'S', 'S', 'S', 'S', 'T', 'T', 'T', 'U', 'U', 'U', 'Ue', 'U', 'U', 'U', 'U', 'U', 'U', 'W', 'Y', 'Y', 'Y', 'Z', 'Z', 'Z', 'a', 'a', 'a', 'a', 'ae', 'a', 'a', 'a', 'a', 'ae', 'c', 'd', 'd', 'e', 'e', 'e', 'e', 'f', 'g', 'g', 'g', 'g', 'h', 'h', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'j', 'k', 'l', 'l', 'l', 'l', 'n', 'n', 'n', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'oe', 'r', 'r', 'r', 's', 's', 's', 't', 't', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'w', 'y', 'y', 'y', 'z', 'z', 'z', 'ss');
	$text = str_replace($search, $replace, $text);
	$permalink = preg_replace('/[^a-zA-Z0-9-_.]/', '', $text);
	if($options == "nodashes") $permalink = mb_strtolower(strtr($permalink, " ", "æ"));
	else $permalink = mb_strtolower(strtr($permalink, " ", "-"));
	$permalink = str_replace("--", "-", $permalink);
	$permalink = str_replace("39", "", $permalink);
	return $permalink;
}

function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false) {
	if($length == 0)
		return '';
	if(strlen($string) > $length) {
		$length -= min($length, strlen($etc));
		if(!$break_words && !$middle) {
			$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
		}
		if(!$middle) {
			return substr($string, 0, $length) . $etc;
		} else {
			return substr($string, 0, $length / 2) . $etc . substr($string, -$length / 2);
		}
	} else {
		return $string;
	}
}

function uppercase($phrase) {
	return strtoupper(str_replace(array('ı', 'i', 'ğ', 'ü', 'ş', 'ö', 'ç'), array('I', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'), $phrase));
}

/* System Messages */
$systemMessages = array();
function resetMessages() {
	global $systemMessages;
	$systemMessages = NULL;
}

function setMessage($text, $type = "default") {
	// Styled types are: default, success, error, warning
	global $systemMessages;
	$systemMessages[$type][] = $text;
}

function printMessage($message, $type="informative") {
	$returnMessage = '<div class="system-messages">' . PHP_EOL;
	$returnMessage .= '<ul class="system-messages-' . $type . '">';
	$returnMessage .= '<li>' . $message . '</li>';
	$returnMessage .= '</ul>' . PHP_EOL;
	$returnMessage .= '</div>';
	print $returnMessage;
}

function getMessages() {
	$returnMessage = '<div class="system-messages">' . PHP_EOL;
	global $systemMessages;
	$messageCount = 0;
	if(count($systemMessages) >= 1) {
		foreach($systemMessages as $type => $items) {
			$returnMessage .= '<ul class="system-messages-' . $type . '">';
			foreach($items as $item) {
				$returnMessage .= '<li>' . $item . '</li>';
				$messageCount++;
			}
			$returnMessage .= '</ul>' . PHP_EOL;
		}
	}
	$returnMessage .= '</div>';
	if($messageCount > 0) return $returnMessage;
	else return '';
}

/* Generators */
function random($length=8, $type='a-zA-Z0-9') {
	$return = $chars = null;

	if(strstr($type, 'a-z'))
		$chars .= 'abcdefghijklmnopqrstuvwxyz';
	if(strstr($type, 'A-Z'))
		$chars .= 'ABCDEFGHIJKLMNOPRQSTUVWXYZ';
	if(strstr($type, '0-9'))
		$chars .= '0123456789';

	for($i = 0, $sl = strlen($chars) - 1; $i <= $length; $i++)
		$return .= $chars[rand(0, $sl)];

	return $return;
}

function getTimeSince($time,$now="") {
	if($now=="") $now = date("Y-m-d H:i:s");
	$difference = strtotime($now)-strtotime($time);
	$since = $difference/60;
	if($since<60) return floor($since)." dakika önce";
	$since = $since/60;
	if($since<24) return floor($since)." saat önce";
	$since = $since/24;
	return floor($since)." gün önce";
}


/* Filters */
function filter($inputType = "string", $inputText = "defaultText", $options = "") {
	if($inputType == "string") {
		return filter_var($inputText, FILTER_SANITIZE_STRING);
	} else if($inputType == "alphanumeric") {
		return preg_replace('/[^a-zA-Z0-9-_]/', '', $inputText);
	} else if($inputType == "int") {
		return filter_var($inputText, FILTER_VALIDATE_INT);
	} else if($inputType == "float") {
		return filter_var($inputText, FILTER_VALIDATE_FLOAT);
	} else if($inputType == "email") {
		return filter_var($inputText, FILTER_SANITIZE_EMAIL);
	} else if($inputType == "url") {
		return filter_var($inputText, FILTER_SANITIZE_URL);
	} else if($inputType == "html") {
		return filter_var($inputText, FILTER_SANITIZE_MAGIC_QUOTES);
	} else if($inputType == "callback") {
		$identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';
		$reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case', 'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 'for', 'switch', 'while', 'debugger', 'function', 'this', 'with', 'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 'private', 'public', 'yield', 'interface', 'package', 'protected', 'static', 'null', 'true', 'false');
		return preg_match($identifier_syntax, $inputText) && !in_array(mb_strtolower($inputText, 'UTF-8'), $reserved_words);
	} else {
		return null;
	}
}

/* Print If Set */
function printIf(&$var, $default = '') {
    print isset($var) ? $var : $default;
}

/* Checked If */
function checkedIf($key,$value) {
	if(isset($key) && isset($value)) {
		if($key==$value) return ' checked="checked"';
		else return '';
	} else {
		return '';
	}
}

/* Selected If */
function selectedIf($key,$value) {
	if(isset($key) && isset($value)) {
		if($key==$value) return ' selected="selected"';
		else return '';
	} else {
		return '';
	}
}

/* Mobile */
function isMobileUser() {
	$mobileAgents = array('android', 'blackberry', 'iphone', 'ipod', 'iemobile', 'opera mobile', 'palmos', 'webos', 'googlebot-mobile');
	if(!empty($_SERVER['HTTP_USER_AGENT'])) {
		$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
		foreach($mobileAgents as $ma) {
			if(strstr($userAgent, $ma)) {
				return true;
			}
		}
	}
	return false;
}


/* BR2NL */
function br2nl($string) {
	$br = preg_match('`<br>[\\n\\r]`', $string) ? '<br>' : '<br />';
	return preg_replace('`' . $br . '([\\n\\r])`', '$1', $string);
}


/* Keep log to file. */
function keepLog($type, $data) {
	$file = DOCUMENTROOT.'logs/'.$type.'.log';
	@file_put_contents($file, $data, FILE_APPEND);
}


/* Full stop. */
function stop($message) {
	die('<html><head><meta charset="utf-8"></head><body style="background-color:#3851B7; color:#fff; font-family:Roboto, Helvetica; text-align:center; margin-top:100px; font-size:12px;"><p>'.$message.'</p><p><a href="javascript:history.back()" style="color:#fff;">Geri Dön</a></p></body></html>');
}

/* Stop Access */
function stopAccess($message=null) {
	stop("Bu işlem için yetkiniz bulunmuyor. ".$message);
}

/* number format (int) */
function format_number($number, $divider = ',') {
	$number = intval(trim($number)) . '';
	$return = '';
	$len = strlen($number);
	for($i = $len - 1, $j = 1; $i >= 0; $i--, $j++) {
		$return = ($j % 3 == 0 && $j != $len ? $divider : '') . $number[$i] . $return;
	}
	return $return;
}

/* Set Page Params */
function setPageParams($param) {
	//echo "gelen"; var_dump($param);
	if(is_array($param) && count($param)>0) {
		foreach($param as $key=>$value) {
			$_REQUEST[$key] = $value; 
		}
	}
	return true;
}

?>