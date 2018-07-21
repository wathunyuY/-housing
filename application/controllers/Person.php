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
			$data = $this->families_model->findByPk($this->path_variable); 
		}else{
			$data = $this->families_model->findAll();
		}
		$this->return_json($data);
	}
	public function add(){
		$processBean =json_decode(file_get_contents('php://input'));
		// $familyRqType = $processBean->familyRqType;
				
		$this->return_json($processBean);
	}

	private function savePicture($base64,$image_name){
		$sp = explode(",", $processBean->picture);
		$head = explode("/", $sp[0]);
		$type = $head[0];
		$type_fianl = $head[1];
		$image = base64_decode($sp[1]);
		// $image_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
		$filename = $image_name . '.' . $type_fianl;

		file_put_contents("assets/picture/".$filename, $image);
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

	private function return_json($val){
		$rs['code'] = 0;
		$rs['data'] = $val;
		echo json_encode($rs);
	}
}
