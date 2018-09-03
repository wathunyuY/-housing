<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: OPTIONS, GET, GET");
header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");
class Person extends CI_Controller {

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

        $this->load->model("person_type_model");
        $this->load->model("person_model");
        $this->load->model("person_current_model");
        $this->load->model("families_model");
        $this->load->model("family_members_model");
        $this->load->model("room_model");
        $this->load->model("family_room_mappings_model");
        $this->load->model("room_status_model");

        $this->controller = $this->uri->segment(2);
        $this->path_variable = $this->uri->segment(3);
        $this->method = $this->input->method();
        $this->HEADER_FAM = "หัวหน้าครอบครัว";
    }  
	public function index()
	{
		
		// $this->load->view('welcome_message');
	}
	public function get(){
		if(null !== $this->path_variable){
			$data = $this->families_model->findByPk($this->path_variable); 
		}else{
			$data = $this->families_model->findAll();
		}
		$this->return_json($data);
	}
	public function add(){
		$processBean =json_decode(file_get_contents('php://input'));
		$rq = $processBean->personRqType;

		if(null != $rq->roomId){
			$roomTbl = $this->room_model->findByPk($rq->roomId);
			if(null == $roomTbl) return;

			$roomTbl["ROOM_STATUS_ID"] = $this->room_status_model->STAY;
			$this->room_model->merge($roomTbl);
			$person = $this->person_model->_new();
			$person["TYPE_ID"] = $rq->person_type;
			$person["BIRTHDAY"] = date('Y-m-d H:i:s',strtotime($rq->birth_date));
			$personTbl = $this->person_model->merge($person);

			if(null != $rq->picture)
				$pic = $this->savePicture($rq->picture,"profile_".$personTbl["PERS_ID"]);
			else $pic = "/assets/picture/default.jpg";

			$personCur = array(
				"PERS_ID"=>$personTbl["PERS_ID"],
				"FIRST_NAME"=>$rq->name,
				"GENDER" => $rq->gender,
				"PERS_N_ID"=> $rq->idCard,
				"NATIONALITY"=> $rq->national,
				"EDUCATION"=>$rq->edu,
				"CAREER"=>$rq->career,
				"ACADEMY"=>$rq->academy,
				"PHONE_NBR"=>$rq->phone,
				"MOBILE_NBR_1"=>$rq->mobile,
				"ADDRESS_1_TYPE0"=>$rq->origin_address_descr,
				"DISTRICT_ID_TYPE0"=>$rq->origin_address,
				"CAR_NUMBER"=>$rq->car,
				"BIKER_NUMBER"=>$rq->biker,
				"REFERENCE"=>$rq->reference,
				"PICTURE_PATH"=> $pic,
				"OWNER_GROUP_ID"=>$rq->owner_group_id
			);
			$personCurTbl = $this->person_current_model->merge($personCur);

			if($rq->is_header_family){// head family
				$family = $this->families_model->_new();
				$family["FAMILY_NAME"]="ครอบครัว ".$personCurTbl["FIRST_NAME"];
				$family["PERS_ID"]=$personTbl["PERS_ID"];
				$familyTbl = $this->families_model->merge($family);

				$frMapping = $this->family_room_mappings_model->_new();
				$frMapping["ROOM_ID"] = $roomTbl["ROOM_ID"];
				$frMapping["FAMILY_ID"] = $familyTbl["FAMILY_ID"];
				$frMapping["START_DATE"] = date('Y-m-d H:i:s',strtotime($rq->start_date));
				$frMapping["END_DATE"] = null;
				$this->family_room_mappings_model->merge($frMapping);
			}else{
				$familyTbl = $this->families_model->findByPk($rq->family_id);
				$familyMember = $this->family_members_model->_new();
				$familyMember["FAMILY_ID"] = $familyTbl["FAMILY_ID"];
				$familyMember["PERS_ID"] = $personTbl["PERS_ID"];
				$familyMember["FAMILY_MEMBER_STATUS"] = $rq->member_status;
				$familyMember["IS_STAY"] = true;
				$familyMember["START_DATE"] = date('Y-m-d H:i:s',strtotime($rq->start_date));
				$this->family_members_model->merge($familyMember);
			}		
		}
		$this->return_json($rq);
	}
	public function edit(){
		$processBean =json_decode(file_get_contents('php://input'));
		$rq = $processBean->personRqType;

		// if(null != $rq->roomId){
		// 	$roomTbl = $this->room_model->findByPk($rq->roomId);
		// 	if(null == $roomTbl) return;

			$person = $this->person_model->findByPk($rq->pers_id);
			// $person["TYPE_ID"] = $rq->person_type;
			unset($person["CURRENT"]); 
			$person["BIRTHDAY"] = date('Y-m-d H:i:s',strtotime($rq->birth_date));
			$personTbl = $this->person_model->merge($person);
			$personCurTbl = $personTbl["CURRENT"];
			if(null != $rq->picture)
				$pic = $this->savePicture($rq->picture,"profile_".$personTbl["PERS_ID"]);
			else $pic = $personCurTbl["PICTURE_PATH"];
			$personCurTbl["FIRST_NAME"]=$rq->name;
			$personCurTbl["GENDER"] = $rq->gender;
			$personCurTbl["PERS_N_ID"]= $rq->idCard;
			$personCurTbl["NATIONALITY"]= $rq->national;
			$personCurTbl["EDUCATION"]=$rq->edu;
			$personCurTbl["CAREER"]=$rq->career;
			$personCurTbl["ACADEMY"]=$rq->academy;
			$personCurTbl["PHONE_NBR"]=$rq->phone;
			$personCurTbl["MOBILE_NBR_1"]=$rq->mobile;
			$personCurTbl["ADDRESS_1_TYPE0"]=$rq->origin_address_descr;
			$personCurTbl["DISTRICT_ID_TYPE0"]=$rq->origin_address;
			$personCurTbl["CAR_NUMBER"]=$rq->car;
			$personCurTbl["BIKER_NUMBER"]=$rq->biker;
			$personCurTbl["REFERENCE"]=$rq->reference;
			$personCurTbl["PICTURE_PATH"]= $pic;
			$personCurTbl["OWNER_GROUP_ID"] = $rq->owner_group_id;
			$personCurTbl = $this->person_current_model->merge($personCurTbl);

			if($rq->is_header_family){// head family
				$family = $this->families_model->findByPk($rq->family_id);
				$family["FAMILY_NAME"]="ครอบครัว ".$personCurTbl["FIRST_NAME"];
				$familyTbl = $this->families_model->merge($family);

				// $frMapping = $this->family_room_mappings_model->_new();
				// $frMapping["ROOM_ID"] = $roomTbl["ROOM_ID"];
				// $frMapping["FAMILY_ID"] = $familyTbl["FAMILY_ID"];
				// $frMapping["START_DATE"] = date('Y-m-d H:i:s',strtotime($rq->start_date));
				// $frMapping["END_DATE"] = null;
				// $this->family_room_mappings_model->merge($frMapping);
			}else{
				// $familyTbl = $this->families_model->findByPk($rq->family_id);
				$familyMember = $this->family_members_model->findByPk($rq->family_id);
				$familyMember["FAMILY_MEMBER_STATUS"] = $rq->member_status;
				$this->family_members_model->merge($familyMember);
			}		
		// }
		$this->return_json($rq);
	}
	public function delete(){
		$id = $this->input->get("id");
		$member = $this->family_members_model->findByPk($id);
		$member["END_DATE"] = $this->dt_now;
		$member["IS_STAY"] = false;
		$this->family_members_model->merge($member);
		$this->return_json([]);
	}
	private function savePicture($base64,$image_name){
		$sp = explode(",", $base64);
		$head = explode("/", $sp[0]);
		$type = $head[0];
		$type_fianl = explode(";",$head[1])[0];
		$image = base64_decode($sp[1]);
		// $image_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
		$filename = $image_name . '.' . $type_fianl;
		file_put_contents("assets/picture/".$filename, $image);
		return "assets/picture/".$filename;
	}

	public function type(){
		switch ($this->path_variable) {
			case 'add':
					$data["TYPE_ID"] = null;
					$data["TYPE_NAME"] = $this->uri->segment(4);
					$data["TYPE_DESCR"] = $this->uri->segment(5);
					$this->person_type_model->merge($data);
				break;
			case 'edit':
					$id = $this->input->get("id");
					$tbl = $this->person_type_model->findByPk($id);
					if(null !==$tbl){
						$tbl["TYPE_NAME"] = $this->uri->segment(4);
						$tbl["TYPE_DESCR"] = $this->uri->segment(5);
						$this->person_type_model->merge($tbl);
					}
				break;
			case 'delete':
					$id = $this->uri->segment(4);
					$tbl = $this->person_type_model->delete($id);
				break;
			
			default:
					if(null !== $this->path_variable){
						$data = $this->person_type_model->findByPk($this->path_variable); 
					}else{
						$data = $this->person_type_model->findAll();
					}
					$this->return_json($data);
				break;
		}
	}

	public function memberDetail(){
		$id = $this->input->get("id");
		$isH = $this->input->get("h");
		$member = $isH == 1? $this->families_model->findByPk($id) : $this->family_members_model->findByPk($id);
		$person = $this->person_model->findByPk($member["PERS_ID"]);
		$roomMap = $isH ==1 ? $this->family_room_mappings_model->findLastFamily($id) : null;
		$cur = $person["CURRENT"];
		$rs = array(
			"pers_id"=> $person["PERS_ID"],
			"member_id"=> $isH == 1 ? $member["FAMILY_ID"] : $member["FAMILY_MEMBER_ID"],
            "fullname" => $cur["FIRST_NAME"],
            "idCard" => $cur["PERS_N_ID"],
            "national" => $cur["NATIONALITY"],
            "edu" => $cur["EDUCATION"],
            "career" => $cur["CAREER"],
            "academy" => $cur["ACADEMY"],
            "mobile" => $cur["MOBILE_NBR_1"],
            "phone" => $cur["PHONE_NBR"],
            "origin_address_descr" => $cur["ADDRESS_1_TYPE0"],
            "origin_address" => $cur["DISTRICT_ID_TYPE0"],
            "car" => $cur["CAR_NUMBER"],
            "biker" => $cur["BIKER_NUMBER"],
            "reference" => $cur["REFERENCE"],
            "gender" => $cur["GENDER"],
            "birth_date" => $person["BIRTHDAY"],
            "start_date" => $isH == 1? $roomMap["START_DATE"] : $member["START_DATE"],
            "relation"=> $isH == 1? $this->HEADER_FAM : $member["FAMILY_MEMBER_STATUS"],
            "is_header"=> $isH ==1,
            "picture_path"=> $cur["PICTURE_PATH"],
            "owner_group_id"=>$cur["OWNER_GROUP_ID"],
            "id"=>$id,
            "isH"=>$isH
		);
		$this->return_json($rs);
	}

	private function return_json($val){
		$rs['code'] = 0;
		$rs['data'] = $val;
		echo json_encode($rs);
	}
}
