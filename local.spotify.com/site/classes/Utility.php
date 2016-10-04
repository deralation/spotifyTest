<?php

class Utility {
	
	private $startPoint = 0;
	private $debug = false;
	private $checkPoints = array();

	static $months = array("January"=>"Ocak","February"=>"Şubat","March"=>"Mart","April"=>"Nisan","May"=>"Mayıs","June"=>"Haziran","July"=>"Temmuz","August"=>"Ağustos","September"=>"Eylül","October"=>"Ekim","November"=>"Kasım","December"=>"Aralık");
	static $days = array("Monday"=>"Pazartesi","Tuesday"=>"Salı","Wednesday"=>"Çarşamba","Thursday"=>"Perşembe","Friday"=>"Cuma","Saturday"=>"Cumartesi","Sunday"=>"Pazar");
	
	public function __construct() {
		$currentPoint = microtime(true);
		$this->startPoint = $currentPoint;
		$this->checkPoints[] = array("time"=>$currentPoint,"name"=>"Start");
	}
	
	public function setDebug($debug) {
		$this->debug = (bool)$debug;
	}

	public function getMonth($i) {
		if($i>=0) $i=$i-1;
		$index = array("Ocak","Şubat","Mart","Nisan","Mayıs","Haziran","Temmuz","Ağustos","Eylül","Ekim","Kasım","Aralık");
		if(is_numeric($i)) return $index[$i];
		else return SELF::$months[$i];
	}

	public function getDay($i) {
		if($i>=0) $i=$i-1;
		$index = array("Pazartesi","Salı","Çarşamba","Perşembe","Cuma","Cumartesi","Pazar");
		if(is_numeric($i)) return $index[$i];
		else return SELF::$days[$i];
	}

	public function formatMoney($input, $format="") {
		setlocale(LC_MONETARY, 'tr_TR');
		if($format=="symbolonly") return "TL";
		else if($input!==null) return money_format('%i', $input);
		else return "";
	}

	public function formatDate($input, $format="Y-m-d H:i:s") {
		if(strtotime($input)>0)
			return str_replace(array_keys(SELF::$months),array_values(SELF::$months),date($format,strtotime($input)));
		else
			return "";
	}

	public function getFirstMonday($date) {
		$dayOfWeek = (int)date("N",strtotime($date));
		//echo $dayOfWeek; //exit();
		if( $dayOfWeek==1) return date("Y-m-d H:i:s",strtotime($date));
		else return date("Y-m-d",strtotime("+".(8-$dayOfWeek)." days",strtotime($date)));
	}

	public function getWeekStartDate($date) {
		$dayOfWeek = date("N",strtotime($date));
		return date("Y-m-d",strtotime("-".($dayOfWeek-1)." days",strtotime($date)));
	}

	public function getStartDateOfWeek($year, $week) {
		$dto = new DateTime();
		$ret['week_start'] = $dto->setISODate($year, $week)->format('Y-m-d');
		$ret['week_end'] = $dto->modify('+6 days')->format('Y-m-d');
		return $ret['week_start'];
	}

	public function formatDataSet($input,$outputFormat="html") {
		try {
			//var_dump($input);
			if($input==null) {
				return "";
			} else if(is_array($input) && $outputFormat=="html") {
				$output = "<dl>";
				foreach($input as $key=>$value) {
					$output.='<dt>'.$key.'</dt>';
					if(is_array($value))
						$output.='<dd>'.json_encode($value).'</dd>';
					else
						$output.='<dd>'.$value.'</dd>';
				}
				$output.="</dl>";
			} else if(is_string($input) || is_numeric($input)) {
				$output = $input;
			} else {
				throw new ExceptionLogger("Bilinmeyen bir data set formatı istendi.");
			}
			return $output;
		} catch(Exception $e) {
			return false;
		}
	}

	
	public function checkPoint($name="untitled") {
		$currentPoint = microtime(true);
		$lastCheckPoint = end($this->checkPoints)["time"];
		$pointDifference = $currentPoint-$lastCheckPoint;
		$this->checkPoints[] = array("time"=>$currentPoint,"name"=>$name);
		if($this->debug) $this->debugMessage("Checkpoint ".$name." at ".$currentPoint.", execution: ".$pointDifference);
	}
	
	public function debugMessage($message) {
		echo "<br />".$message;
	}

	public function getPager($currentPage=1,$totalPages=1,$urlPrefix="?") {

		// If there is a question mark add & otherwise add ?
		if (strpos($urlPrefix, '?') !== false) $urlPrefix.="&paging=";
		else $urlPrefix.="?paging=";

		if($currentPage>$totalPages && $totalPages>0)
			throw new ExceptionLogger("Current page cannot be bigger than total pages in pager.");
		
		if($totalPages<2)
			return '';

		// Generate Pager HTML using Parameters
		$pager = '<div class="paging">
						<ul class="paging-numbers">'.PHP_EOL;
		if($currentPage>1) $pager.='<li class="paging-previous"><a href="'.$urlPrefix.'1">&larr;</a></li>'.PHP_EOL;
		if($currentPage>1) $pager.='<li class="paging-first"><a href="'.$urlPrefix.'1">1</a></li>'.PHP_EOL;
		if($currentPage>=5) $pager.='<li class="paging-more">&middot;&middot;&middot;</li>'.PHP_EOL;
		if($currentPage>3) $pager.='<li class="paging-sibling"><a href="'.$urlPrefix.($currentPage-2).'">'.($currentPage-2).'</a></li>'.PHP_EOL;
		if($currentPage>2) $pager.='<li class="paging-sibling"><a href="'.$urlPrefix.($currentPage-1).'">'.($currentPage-1).'</a></li>'.PHP_EOL;
		$pager.='<li class="paging-current">'.$currentPage.'</li>'.PHP_EOL;
		if($currentPage+1<$totalPages) $pager.='<li class="paging-sibling"><a href="'.$urlPrefix.($currentPage+1).'">'.($currentPage+1).'</a></li>'.PHP_EOL;
		if($currentPage+2<$totalPages) $pager.='<li class="paging-sibling"><a href="'.$urlPrefix.($currentPage+2).'">'.($currentPage+2).'</a></li>'.PHP_EOL;
		if($currentPage+3<$totalPages) $pager.='<li class="paging-more">&middot;&middot;&middot;</li>'.PHP_EOL;
		if($currentPage<$totalPages) $pager.='<li class="paging-last"><a href="'.$urlPrefix.$totalPages.'">'.$totalPages.'</a></li>'.PHP_EOL;
		if($currentPage<$totalPages) $pager.='<li class="paging-next"><a href="'.$urlPrefix.$totalPages.'">&rarr;</a></li>'.PHP_EOL;
		$pager.='
						</ul>
					</div>';
		return $pager;
	}
	
	/*
	 * Validate data
	 */
	public function validate() {
		
	}
}
?>
	