<?php
class Activity extends Thing {

	static $types  = array(
		1 	=> array(
			"code"	=> "event.user.login",
			"text"	=> "Giriş yaptı."
		),
		2	=> array(
			"code"	=> "event.user.logout",
			"text"	=> "Çıkış yaptı"
		),
		3	=> array(
			"code"	=> "event.reservation.open",
			"text"	=> "Rezervasyon oluşturdu."
		),
		4	=> array(
			"code"	=> "event.reservation.close",
			"text"	=> "Rezervasyon kapattı"
		),
		5	=> array(
			"code"	=> "event.member.create",
			"text"	=> "Üyelik oluşturdu."
		),
		6	=> array(
			"code"	=> "event.member.active",
			"text"	=> "Üyelik aktifleştirdi."
		),
		7	=> array(
			"code"	=> "event.member.bypass3HourRule",
			"text"	=> "3 Saat kuralını değiştirdi."
		),
		8	=> array(
			"code"	=> "event.member.charge",
			"text"	=> "Üyeden çekim yaptı."
		),
		9	=> array(
			"code"	=> "event.vehicle.assignCard",
			"text"	=> "Kart ataması yaptı."
		),
		10	=> array(
			"code"	=> "event.member.bonus.add",
			"text"	=> "Kredi yüklemesi yaptı."
		),
		11	=> array(
			"code"	=> "event.member.bonus.sub",
			"text"	=> "Kredi kullandırdı."
		),
		12	=> array(
			"code"	=> "event.reservation.freeClose",
			"text"	=> "Ücretsiz rezervasyon kapaması yaptı."
		),
		13	=> array(
			"code"	=> "event.payment.provision.refund",
			"text"	=> "Provizyon iadesi yaptı."
		),
		14	=> array(
			"code"	=> "event.payment.sale.refund",
			"text"	=> "Çekim iadesi yaptı."
		),
		15	=> array(
			"code"	=> "event.reservation.resetStartKm",
			"text"	=> "Başlangıç kilometresini sıfırladı."
		),
		16	=> array(
			"code"	=> "event.reservation.forceChange",
			"text"	=> "Rezervasyonda kontrolsüz değişiklik yaptı."
		),
		17	=> array(
			"code"	=> "event.member.edit",
			"text"	=> "Üyenin bilgilerini güncelledi"
		),
		18	=> array(
			"code"	=> "event.member.add",
			"text"	=> "Üyelik yarattı"
		),
		19	=> array(
			"code"	=> "event.member.creditcard.add",
			"text"	=> "Kredi kartı ekledi"
		),
		20	=> array(
			"code"	=> "event.member.creditcard.edit",
			"text"	=> "Kredi kartı düzenledi"
		),
		21	=> array(
			"code"	=> "event.member.yoyocard.add",
			"text"	=> "Üyeye YOYO Kart tanımladı"
		),
		22	=> array(
			"code"	=> "event.member.yoyocard.remove",
			"text"	=> "Üyeden YOYO Kart kaldırdı"
		),
		23	=> array(
			"code"	=> "event.member.lead.update",
			"text"	=> "Potansiyel üye durumu güncelledi"
		),
		24	=> array(
			"code"	=> "event.member.group.add",
			"text"	=> "Üye grubu ekledi"
		),
		25	=> array(
			"code"	=> "event.member.group.edit",
			"text"	=> "Üye grubu düzenledi"
		),
		26	=> array(
			"code"	=> "event.blog.post.add",
			"text"	=> "Blog yazısı ekledi"
		),
		27	=> array(
			"code"	=> "event.blog.post.edit",
			"text"	=> "Blog yazısı düzenledi"
		),
		28	=> array(
			"code"	=> "event.post.category.add",
			"text"	=> "Blog kategorisi ekledi"
		),
		29	=> array(
			"code"	=> "event.post.category.edit",
			"text"	=> "Blog kategorisi düzenledi"
		),
		30	=> array(
			"code"	=> "event.campaign.add",
			"text"	=> "Kampanya yarattı"
		),
		31	=> array(
			"code"	=> "event.campaign.edit",
			"text"	=> "Kampanya bilgisi düzenledi"
		),
		32	=> array(
			"code"	=> "event.file.upload",
			"text"	=> "Dosya yükledi"
		),
		33	=> array(
			"code"	=> "event.vehicle.sort",
			"text"	=> "Araçları sıraladı"
		),
		34	=> array(
			"code"	=> "event.finance.payment.save",
			"text"	=> "Ödeme kaydı yarattı"
		),
		35	=> array(
			"code"	=> "event.finance.payment.refund",
			"text"	=> "Ödeme iade etti"
		),
		36	=> array(
			"code"	=> "event.finance.payment.cancel",
			"text"	=> "Ödeme iptal etti"
		),
		37	=> array(
			"code"	=> "event.location.sort",
			"text"	=> "Lokasyonları sıraladı"
		),
		38	=> array(
			"code"	=> "event.finance.payment.auth",
			"text"	=> "Provizyon aldı"
		),
		39	=> array(
			"code"	=> "event.outOfService.add",
			"text"	=> "Servis dışı ekledi."
		),
		40	=> array(
			"code"	=> "event.outOfService.edit",
			"text"	=> "Servis dışı düzenledi."
		),	
		41	=> array(
			"code"	=> "event.outOfService.delete",
			"text"	=> "Servis dışı sildi."
		),
		42 	=> array(
			"code"	=> "event.ticket.category.add",
			"text"	=> "Ticket kategorisi eklendi."
			),
		43 => array(
			"code"	=> "event.ticket.category.edit",
			"text"	=> "Ticket kategori bilgisi düzeltildi."
			)
	);

	public function __construct() {
		$this->name = "activity";
		$this->table = "EventLog";
		$this->prefix = "Activity";
		$this->fields = array(
			"id" => array(
				"name" 		=> "id",
				"type" 		=> "int",
				"field"  	=> "id",
				"required"	=> true
			),
			"userIP" => array(
				"name" 		=> "userIP",
				"type" 		=> "string",
				"field"		=> "ip",
				"required"	=> false
			),
			"userAgent" => array(
				"name" 		=> "userAgent",
				"type"		=> "string",
				"field" 	=> "userAgent",
				"required"	=> false
			),
			"typeID" => array(
				"name" 		=> "typeID",
				"type"		=> "int",
				"field" 	=> "typeId",
				"required"	=> true
			),
			"creationDate" => array(
				"name" 		=> "creationDate",
				"type"		=> "datetime",
				"field" 	=> "createDate",
				"required"	=> false
			),
			"actionUserID" => array(
				"name" 		=> "actionUserID",
				"type"		=> "int",
				"field" 	=> "userId",
				"required"	=> false
			),
			"objectMemberID" => array(
				"name" 		=> "objectMemberID",
				"type" 		=> "int",
				"field"		=> "memberId",
				"required"	=> false
			),
			"objectReservationID" => array(
				"name" 		=> "objectReservationID",
				"type" 		=> "int",
				"field"		=> "reservationId",
				"required"	=> false
			),
			"objectYoyoCardID" => array(
				"name" 		=> "objectYoyoCardID",
				"type" 		=> "int",
				"field"		=> "yoyoCardId",
				"required"	=> false
			),
			"objectPaymentID" => array(
				"name" 		=> "objectPaymentID",
				"type" 		=> "int",
				"field"		=> "paymentId",
				"required"	=> false
			),
			"objectVehicleID" => array(
				"name" 		=> "objectVehicleID",
				"type" 		=> "int",
				"field"		=> "vehicleId",
				"required"	=> false
			)
		);
		$this->relations = array(
			"typeID" => array(
				"class"		=> null,
				"table"		=> null,
				"field"		=> "id"
			),
			"actionUserID" => array(
				"class"		=> "User",
				"table"		=> "User",
				"field"		=> "id"
			)
		);
		$this->searchable = array(
			"id" => "equal"
		);
		$this->sortingField = "creationDate";
		$this->order = "desc";
		$this->deleteAction = array(
			"field" => "status",
			"flag" => "deleted"
		);
	}

	public function get($param=null) {
		return $this->arrange(PARENT::get($param));
	}

	public function getEventLog($id) {
		global $database;
		$database->select("SELECT User.id AS userID, User.firstName AS firstName, User.lastName AS lastName, EventLog.* FROM EventLog LEFT JOIN User ON User.id=EventLog.userId WHERE EventLog.id=".$this->filter("int",$id)." LIMIT 1");
		$r = $database->getOne();
		if(count($r)>0) {
			$r["name"] = SELF::$types[$r["typeId"]]["text"];
			$r["user"]["firstName"] = $r["firstName"];
			$r["user"]["lastName"] = $r["lastName"];
			$r["user"]["id"] = $r["userID"];
			$r["date"] = $r["createDate"];
			$r["objectReservationID"] = $r["reservationId"];
			$r["objectPaymentID"] = $r["paymentId"];
			$r["objectMemberID"] = $r["memberId"];
			$r["objectVehicleID"] = $r["vehicleId"];
		}
		//echo "<pre>";var_dump($return); exit();
		return $r;
	}

	public function getEventLogs($limit=10) {
		global $database;
		$database->select("SELECT User.firstName AS firstName, User.lastName AS lastName, EventLog.id AS id, EventLog.createDate AS creationDate, EventLog.typeId FROM EventLog LEFT JOIN User ON User.id=EventLog.userId WHERE EventLog.userId>0 ORDER BY EventLog.id DESC LIMIT ".(int)$limit);
		$return = $database->getAll();
		if(count($return)>0) {
			foreach($return as $key=>$r) {
				$return[$key]["name"] = SELF::$types[$r["typeId"]]["text"];
				$return[$key]["user"]["firstName"] = $r["firstName"];
				$return[$key]["user"]["lastName"] = $r["lastName"];
			}
		}
		//echo "<pre>";var_dump($return); exit();
		return $return;
	}

	public function log($type,$objects=null,$user=null) {

		$new = array();

		$typeID = $this->getTypeID($type);
		if($typeID===false) {
			throw new ExceptionLogger("Bilinmeyen bir etkinlik ID'si parametre olarak gönderildi.");
			return false;
		}
		$new["typeID"] = $typeID;

		if($user==null) {
			global $user;
			$u = $user->active;
		} else if(isset($user["id"])) {
			$u = $user;
		} else if(is_numeric($user)) {
			$temp = new User();
			$u = $temp->getOne($user);
		} else {
			throw new ExceptionLogger("Unhandled case for activity log user parameter");
			return false;
		}
		$new["actionUserID"] = $u["id"];


		$event = explode(".",$type);
		if($event[1]=="member") {
			if(is_numeric($objects)) {
				$new["objectMemberID"] = $objects;
			} else if(isset($objects["id"])) {
				$new["objectMemberID"] = $objects["id"];
			} else if(is_array($objects)) {
				if(isset($objects["memberID"])) 	$new["objectMemberID"] = $objects["memberID"];
				if(isset($objects["paymentID"])) 	$new["objectPaymentID"] = $objects["paymentID"];
			} else {
				// no member data on memberleads
				//throw new ExceptionLogger("Cannot handle object parameter for this activity log");
			}
		} else if($event[1]=="reservation") {
			if(is_numeric($objects)) {
				$new["objectReservationID"] = $objects;
			} else if(isset($objects["id"])) {
				$new["objectReservationID"] = $objects["id"];
			} else if(is_array($objects)) {
				if(isset($objects["memberID"])) 	$new["objectMemberID"] = $objects["memberID"];
				if(isset($objects["reservationID"])) $new["objectReservationID"] = $objects["reservationID"];
				if(isset($objects["paymentID"])) 	$new["objectPaymentID"] = $objects["paymentID"];
			} else {
				throw new ExceptionLogger("Cannot handle object parameter for this activity log");
			}
		} else if($event[1]=="finance" && $event[2]=="payment") {
			if(is_numeric($objects)) {
				$new["objectPaymentID"] = $objects;
			} else if(isset($objects["id"])) {
				$new["objectPaymentID"] = $objects["id"];
			} else if(is_array($objects)) {
				if(isset($objects["memberID"])) 	$new["objectMemberID"] = $objects["memberID"];
				if(isset($objects["paymentID"])) 	$new["objectPaymentID"] = $objects["paymentID"];
			} else {
				throw new ExceptionLogger("Cannot handle object parameter for this activity log");
			}
		} else if($event[1]=="vehicle") {
			if(is_numeric($objects)) {
				$new["objectVehicleID"] = $objects;
			} else if(isset($objects["id"])) {
				$new["objectVehicleID"] = $objects["id"];
			} else {
				// No vehicle object for sorting action
			}
		} else if($event[1]=="outOfService") {
			if(is_numeric($objects)) {
				$new["objectVehicleID"] = $objects;
			} else if(isset($objects["vehicleID"])) {
				$new["objectVehicleID"] = $objects["vehicleID"];
			} else {
				// No vehicle object for sorting action
			}
		} else {
			// No object?
		}

		$new["userIP"] = $this->getUserIP();
		$new["userAgent"] = $this->getUserAgent();
		
		
		return $this->add($new);
	}

	public function getTypeID($eventCode) {
		foreach(SELF::$types as $eventID=>$t) {
			if($eventCode==$t["code"]) return $eventID;
		}
		throw new ExceptionLogger("Unknown event code: ".$eventCode);
		return false;
	}

	public function add($data) {
		try {
			$this->create($data);
			return $this->persist();
		} catch(Exception $e) {
			$this->errorMesssage = $e->getMessage();
			return false;
		}
	}

	public function getUserIP() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	      $ip=$_SERVER['HTTP_CLIENT_IP'];
	    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	    } else {
	      $ip=$_SERVER['REMOTE_ADDR'];
	    }
	    return $ip;
	}

	public function getUserAgent() {
		if(isset($_SERVER["HTTP_USER_AGENT"])) {
			return $_SERVER["HTTP_USER_AGENT"];
		} else {
			return null;
		}
	}
}
?>