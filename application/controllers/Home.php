<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: OPTIONS, GET, GET");
header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");
class Home extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct()
    {
        // Construct the parent class
        parent::__construct(); 
        // Configure limits on our controller methods

        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
        // Set Date
        date_default_timezone_set('Asia/Bangkok');
        $now = new DateTime(null, new DateTimeZone('Asia/Bangkok')); 
        $this->dt_now = $now->format('Y-m-d H:i:s');
        $this->day_now = $now->format('d/m/Y'); 
        $this->ymdHis   = $now->format('ymdHis');
        $this->dmyHis   = $now->format('d-m-Y H:i:s');
        $this->milliseconds = round(microtime(true) * 1000); 
        $this->time = $now->format('H:i:s');
        $this->load->helper("url");

        $this->load->model("general_model");
        $this->load->model("families_model");
        $this->load->model("home_model");
        $this->load->model("home_section_model");
        $this->load->model("room_model");
        $this->load->model("room_status_model");
        $this->load->model("home_type_model");
        $this->load->model("owner_group_model");
        $this->load->model("family_room_mappings_model");
        $this->load->model("family_members_model");

        $this->controller = $this->uri->segment(2);
        $this->path_variable = $this->uri->segment(3);
        $this->method = $this->input->method();
    }  
	public function index()
	{
		// $this->load->view('welcome_message');
	}
	public function get(){
		if(null !== $this->path_variable){
			$data = $this->home_model->findByPk($this->path_variable); 
		}else{
			$data = $this->home_model->findAll();
		}
		$this->return_json($data);
	}
	public function add(){
		$processBean =json_decode(file_get_contents('php://input'));
		$homeRqType = $processBean->homeRqType;
		$homeTbl['HOME_ID'] = $homeRqType->homeId;
		$homeTbl['HOME_NAME'] = $homeRqType->homeName;
		$homeTbl['HOME_DESCR'] = $homeRqType->homeDescr;
		$homeTbl['HOME_TYPE_ID'] = $homeRqType->homeTypeId;
		$homeTbl['OWNER_GROUP_ID'] = $homeRqType->ownerGroupId;
		$homeTbl['CREATE_DATE'] =  $this->dt_now;
		// $homeTbl['sec'] = array();
		$homeTbl = $this->home_model->merge($homeTbl);
		if(count($homeRqType->sections)){
			foreach ($homeRqType->sections as $key => $sections) {
				$sectionTbl['HOME_SECTION_ID'] = $sections->sectionId;
				$sectionTbl['HOME_ID'] = $homeTbl['HOME_ID'];
				$sectionTbl['HOME_SECTION_NAME'] = $sections->sectionName;
				$sectionTbl['HOME_SECTION_ORDER'] = $sections->sectionOrder;
				$sectionTbl = $this->home_section_model->merge($sectionTbl);
				// $sectionTbl['rms'] = array();
				if(count($sections->rooms)){
					foreach ($sections->rooms as $key => $room) {
						$roomTbl['ROOM_ID'] = $room->roomId;
						$roomTbl['HOME_SECTION_ID'] = $sectionTbl['HOME_SECTION_ID'];
						$roomTbl['ROOM_NAME'] = $room->roomName;
						$roomTbl['ROOM_ORDER'] = $room->roomOrder;
						$roomTbl['ROOM_ADDRESS'] = $room->roomAddress;
						$roomTbl['ROOM_SUB_ADDRESS'] = $room->roomSubAddress;
						$roomTbl['ROOM_SEQ'] = $room->roomSeq;
						$roomTbl['ROOM_STATUS_ID'] = $room->roomStatusId;
						$roomTbl['OWNER_GROUP_ID'] = $room->ownerGroupId;
						$roomTbl = $this->room_model->merge($roomTbl);
						// array_push($sectionTbl['rms'], $roomTbl);
					}
				}
				// array_push($homeTbl['sec'], $sectionTbl);
			}
		}
		$this->return_json($homeTbl);
	}

	public function section(){
		switch ($this->path_variable) {
			case 'add':
				$processBean =json_decode(file_get_contents('php://input'));
				$secRqType = $processBean->secRqType;
				$sectionTbl['HOME_SECTION_ID'] = $secRqType->sectionId;
				$sectionTbl['HOME_ID'] = $secRqType->homeId;
				$sectionTbl['HOME_SECTION_NAME'] = $secRqType->sectionName;
				$sectionTbl['HOME_SECTION_ORDER'] = $secRqType->sectionOrder;
				$sectionTbl = $this->home_section_model->merge($sectionTbl);
				if(count($secRqType->rooms)){
					foreach ($secRqType->rooms as $key => $room) {
						$roomTbl['ROOM_ID'] = $room->roomId;
						$roomTbl['HOME_SECTION_ID'] = $sectionTbl['HOME_SECTION_ID'];
						$roomTbl['ROOM_NAME'] = $room->roomName;
						$roomTbl['ROOM_ORDER'] = $room->roomOrder;
						$roomTbl['ROOM_ADDRESS'] = $room->roomAddress;
						$roomTbl['ROOM_SUB_ADDRESS'] = $room->roomSubAddress;
						$roomTbl['ROOM_SEQ'] = $room->roomSeq;
						$roomTbl['ROOM_STATUS_ID'] = $room->roomStatusId;
						$roomTbl['OWNER_GROUP_ID'] = $room->ownerGroupId;
						$roomTbl = $this->room_model->merge($roomTbl);
						// array_push($sectionTbl['rms'], $roomTbl);
					}
				}
				break;
			
			default:
				# code...
				break;
		}
		$this->return_json(array("status"=>0));
	}
	public function room(){
		switch ($this->path_variable) {
			case 'add':
				$processBean =json_decode(file_get_contents('php://input'));
				$room = $processBean->roomRqType;
				$roomTbl['ROOM_ID'] = $room->roomId;
				$roomTbl['HOME_SECTION_ID'] = $room->sectionId;
				$roomTbl['ROOM_NAME'] = $room->roomName;
				$roomTbl['ROOM_ORDER'] = $room->roomOrder;
				$roomTbl['ROOM_ADDRESS'] = $room->roomAddress;
				$roomTbl['ROOM_SUB_ADDRESS'] = $room->roomSubAddress;
				$roomTbl['ROOM_SEQ'] = $room->roomSeq;
				$roomTbl['ROOM_STATUS_ID'] = $room->roomStatusId;
				$roomTbl['OWNER_GROUP_ID'] = $room->ownerGroupId;
				$roomTbl = $this->room_model->merge($roomTbl);
				break;
			
			default:
				# code...
				break;
		}
		$this->return_json(array("status"=>0));
	}

	public function type(){
		switch ($this->path_variable) {
			case 'add':
					$data["HOME_TYPE_ID"] = null;
					$data["HOME_TYPE_NAME"] = $this->uri->segment(4);
					$data["HOME_TYPE_DESCR"] = $this->uri->segment(5);
					$this->home_type_model->merge($data);
				break;
			case 'edit':
					$id = $this->input->get("id");
					$tbl = $this->home_type_model->findByPk($id);
					if(null !==$tbl){
						$tbl["HOME_TYPE_NAME"] = $this->uri->segment(4);
						$tbl["HOME_TYPE_DESCR"] = $this->uri->segment(5);
						$this->home_type_model->merge($tbl);
					}
				break;
			case 'delete':
					$id = $this->uri->segment(4);
					$tbl = $this->home_type_model->delete($id);
				break;
			
			default:
					if(null !== $this->path_variable){
						$data = $this->home_type_model->findByPk($this->path_variable); 
					}else{
						$data = $this->home_type_model->findAll();
					}
					$this->return_json($data);
				break;
		}
	}

	public function masterData(){
		$home_type = $this->home_type_model->findAll();
		$room_status = $this->room_status_model->findAll();
		$rs = array(
			"home_type"=>$home_type,
			"room_status"=>$room_status
		);
		$this->return_json($rs);
	}

	public function login(){
		$u = $this->input->post("username");
		$p = $this->input->post("password");
		// if( ($u == "admin" && $p == "@1234")){
		// 	$this->load->view('home_index');
		// }else{
		// 	// $this->load->view('login');
		// }
		$this->return_json(array("result"=>$u == "admin" && $p == "@1234"));
	}

	public function ownerGroups(){
		$data = $this->owner_group_model->findAll();
		foreach ($data as $key_g => $own) {
			$homes = $this->home_model->findByColumn($this->owner_group_model->PK,$own[$this->owner_group_model->PK]);
			foreach ($homes as $key_h => $hm) {
				$secs= $this->home_section_model->findByColumn($this->home_model->PK,$hm[$this->home_model->PK]);
				foreach ($secs as $key_s => $sec) {
					$rooms = $this->room_model->findByColumn($this->home_section_model->PK,$sec[$this->home_section_model->PK]);
					$secs[$key_s]["rooms"] = $rooms;
				}
				$homes[$key_h]["sections"] = $secs;
			}
			$data[$key_g]["homes"]=$homes;
		}
		$this->return_json($data);
	}

	public function roomDetail(){
		$roomId = $this->input->get("room_id");
		$roomMap = $this->family_room_mappings_model->findByRoom($roomId);
		if(null == $roomMap) {
			$data["find"] = false;
			$this->return_json($data);
			return;
		} 
		$roomTbl = $roomMap["ROOM"];
		$roomStatusTbl = $this->room_status_model->findByPk($roomTbl["ROOM_STATUS_ID"]);
		$sectionTbl = $this->home_section_model->findByPk($roomTbl["HOME_SECTION_ID"]);
		$homeTbl = $this->home_model->findByPk($sectionTbl["HOME_ID"]);
		$family = $roomMap["FAMILY"];
		$family["start_date"] = $roomMap["START_DATE"];
		$data["find"] = true;
		$data["room_status"] = $roomStatusTbl;
		$data["room_id"] = $roomTbl["ROOM_ID"];
		$data["room_name"] = $roomTbl["ROOM_NAME"];
		$data["room_address"] = $roomTbl["ROOM_ADDRESS"];
		$data["room_sub_address"] = $roomTbl["ROOM_SUB_ADDRESS"];
		$data["section"] = array(
			"name"=> $sectionTbl["HOME_SECTION_NAME"],
			"id"=>$sectionTbl["HOME_SECTION_ID"],
			"order"=>$sectionTbl["HOME_SECTION_ORDER"]
		);
		$data["home"] = array(
			"name"=> $homeTbl["HOME_NAME"],
			"id"=>$homeTbl["HOME_ID"],
			"descr"=>$homeTbl["HOME_DESCR"]
		);
		$data["family"] = $family;
		$this->return_json($data);
	}

	private function return_json($val){
		$rs['code'] = 0;
		$rs['data'] = $val;
		echo json_encode($rs);
	}

}
