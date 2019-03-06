<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: OPTIONS, GET, GET ,DELETE,POST");
header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");
class Statistic extends CI_Controller {

	function __construct()
    {
        parent::__construct(); 

        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
        date_default_timezone_set('Asia/Bangkok');
        $now = new DateTime(null, new DateTimeZone('Asia/Bangkok')); 
        $this->dt_now = $now->format('Y-m-d H:i:s');
        $this->day_now = $now->format('d/m/Y'); 
        $this->ymdHis   = $now->format('ymdHis');
        $this->dmyHis   = $now->format('d-m-Y H:i:s');
        $this->milliseconds = round(microtime(true) * 1000); 
        $this->time = $now->format('H:i:s');
        $this->load->helper("url");

        $this->load->model("owner_group_model");

        $this->controller = $this->uri->segment(2);
        $this->path_variable = $this->uri->segment(3);
        $this->method = $this->input->method();
    }  

    public function index(){
        $owner_group_id = $this->input->get("owner_group_id");
        $rs = [
            "careers"=>$this->owner_group_model->getPopCareerByOwnerId($owner_group_id),
            "ages"=>[
                "value"=> $this->owner_group_model->getAgeRankByOwnerId($owner_group_id),
                "name"=>[
                    "_12"=> "ปฐมวัย(0-12)",
                    "_12_20"=>"วัยรุ่น(12-20)",
                    "_20_40"=>"วัยผู้ใหญ่(20-45)",
                    "_40_60"=>"วัยกลางคน(40-60)",
                    "_60"=>"วัยสูงอายุ(60 ปีขึ้นไป)"]
            ],
            "sexs"=>$this->owner_group_model->getPopGenderByOwnerId($owner_group_id)
        ];
        $this->return_json($rs);
    }

	private function return_json($val){
		$rs['code'] = 0;
		$rs['data'] = $val;
		echo json_encode($rs);
	}
}
