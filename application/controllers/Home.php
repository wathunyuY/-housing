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
        $this->load->model("home_type_model");

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

	public function login(){
		$u = $this->input->post("username");
		$p = $this->input->post("password");
		if( ($u == "admin" && $p == "@1234")){
			$this->load->view('home_index');
		}else{
			// $this->load->view('login');
		}
	}

	private function return_json($val){
		$rs['code'] = 0;
		$rs['data'] = $val;
		echo json_encode($rs);
	}
}
