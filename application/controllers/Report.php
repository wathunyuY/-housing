<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: OPTIONS, GET, GET ,DELETE,POST");
header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");
class Report extends CI_Controller {

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
        $this->load->model("person_model");
        $this->load->model("address_model");


        $this->controller = $this->uri->segment(2);
        $this->path_variable = $this->uri->segment(3);
        $this->method = $this->input->method();
    }  
	public function index()
	{
		// $this->load->view('welcome_message');
	}

	private function return_json($val){
		$rs['code'] = 0;
		$rs['data'] = $val;
		echo json_encode($rs);
	}

	public function pdf(){
		$this->load->library('pdf_nohead'); // 
		$this->pdf_nohead->fontpath = 'fonts/'; // Create folder fonts at Codeigniter
		$this->pdf_nohead->AddPage();
		$this->pdf_nohead->AddFont('angsa','','angsa.php');
		$this->pdf_nohead->AddFont('angsa','B','angsab.php');
		$this->pdf_nohead->AddFont('angsa','I','angsai.php');
		$this->pdf_nohead->AddFont('angsa','U','angsaz.php');
		$this->pdf_nohead->SetFont('angsa','U',18);
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','แบบรายงานขอบ้านพักอาศัย'),0,1,'C');
		$this->pdf_nohead->SetFont('angsa','',16);	
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','หน่วย'.$this->getDot(70)),0,1,'R');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','วันที่'.$this->getDot().'เดือน'.$this->getDot().'พ.ศ'.$this->getDot()),0,1,'R');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','เรื่อง        ขอบ้านพักอาศัยของทางราชการ'),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','เรียน'.$this->getDot(100)),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','                กระผม/ดิฉัน'.$this->getDot(90).'ตำแหน่ง'.$this->getDot(50)),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','สังกัด'.$this->getDot(50).'รับเงินเดือน'.$this->getDot(50).'เดือนละ'.$this->getDot(50)),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','กับเงินพิเศษอื่นๆ เดือนละ'.$this->getDot(50).'รวมรายได้'.$this->getDot(50).'บาท'),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','                ภรรยา/สามี ชื่อ'.$this->getDot(70).'ทำงาน'.$this->getDot(70)),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','รวมรายได้เดือนละ'.$this->getDot(50).' และมีบุตร จำนวน'.$this->getDot(30).'คน'),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','                ปัจจุบันกระผม/ดิฉัน อาศัยอยู่บ้านเลขที่'.$this->getDot(55).'หมู่ที่'.$this->getDot(50)),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','ตำบล/แขวง'.$this->getDot(50).'อำเภอ/เขต'.$this->getDot(50).'จังหวัด'.$this->getDot(45)),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','ตั้งแต่'.$this->getDot(50).'เช่าบ้าน (ถ้ามี) อยู่ที่'.$this->getDot(50).'เดือนละ'.$this->getDot(30).'บาท'),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','โดย (เบิกหรือไม่เบิก) '.$this->getDot(50).'เดือนละ'.$this->getDot(30).'บาท'),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','ตามสิทธิ์เบิกได้เดือนละ '.$this->getDot(30).'บาทและได้แนบหลักฐานการเช่าซึ่งผู้บังคับบัญชาเซ็นต์รับรองเรียบร้อยแล้ว.-'),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','                จำนวนคนในครอบครัวกระผม/ดิฉัน ที่จะเข้าพักอาศัยมีจำนวนทั้งหมด'.$this->getDot(30).'คน ดังนี้'),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','๑.   '.$this->getDot(90)),0,1,'C');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','๒.   '.$this->getDot(90)),0,1,'C');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','๓.   '.$this->getDot(90)),0,1,'C');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','๔.   '.$this->getDot(90)),0,1,'C');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','                กระผม/ดิฉัน สัญญาว่าจะปฏิบัติตามระเบียบฯ การเข้าพักอาศัยในบ้านพักของทางราชการ ทุกประเภท.-'),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','                จึงเรียนมาเพื่อทราบ และพิจารณาดำเนินการต่อไป'),0,1,'L');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','ควรมิควรแล้วแต่จะกรุณา               '),0,1,'R');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','               '.$this->getDot(70)),0,1,'R');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','               ('.$this->getDot(70).')'),0,1,'R');
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','ตำแน่ง'.$this->getDot(80)),0,1,'R');


		$this->pdf_nohead->Output("test.pdf","F");

		$this->load->helper('download');

		$data = file_get_contents("test.pdf");
		$name = "แบบรายงานขอบ้านพักอาศัย/pdf.pdf";

		force_download($name, $data); 
		//echo anchor('MyPDF/test.pdf', 'Download');
	}

	public function report(){
		$this->load->library('pdf'); // 
		$this->pdf->fontpath = 'fonts/'; // Create folder fonts at Codeigniter
		$ownerId = $this->path_variable;
		$homeId = $this->input->get("home");
		$all = $this->home_model->findHomeReport($ownerId,$homeId);
		$fontSize16 = 16;
		$this->pdf->SetMargins(20,10,20);
		$this->pdf->AliasNbPages();
		$this->pdf->AddPage();
		$this->pdf->AddFont('angsa','','angsa.php');
		$this->pdf->AddFont('angsa','B','angsab.php');
		$this->pdf->AddFont('angsa','I','angsai.php');
		$this->pdf->AddFont('angsa','U','angsaz.php');
		$this->pdf->SetFont('angsa','U',18);
		$this->pdf->Cell(0,10,iconv( 'UTF-8','TIS-620','รายงานบ้านพักอาศัย'),0,1,'C');
		$this->pdf->SetFont('angsa','',14);	
		$this->pdf->Cell(0,10,iconv( 'UTF-8','TIS-620','ทะเบียนบ้านพักค่ายบุญรังษี (ต.พงสวาย)'),0,1,'C');
		foreach ($all as $key => $home) {
			$secs = $home["SECS"];
			$this->pdf->SetFont('angsa','',14);	
			// $this->pdf->Cell(0,10,iconv( 'UTF-8','TIS-620','ทะเบียนบ้านพักค่ายบุญรังษี (ต.พงสวาย)'),0,1,'C');
			$this->pdf->Cell(0,10,iconv( 'UTF-8','TIS-620','ของอาคาร '.$home["OWNER_GROUP_DESCR"].' รับผิดชอบ'),0,1,'C');
			$w = array(10, 20, 30, 40,30,50);
			$header = ["ลำดับ","บ้านเลขที่","หมายเลขอาคาร","รายชื่อเข้าพักอาศัย","สังกัด","หมายเหตุ"];
			for($i=0;$i<count($header);$i++)
			    $this->pdf->Cell($w[$i],7,iconv( 'UTF-8','TIS-620',$header[$i]),1,0,'C');
			$this->pdf->Ln();
			

			foreach ($secs as $key_sec => $sec){
				$rooms = $sec["ROOMS"];
				$this->pdf->Cell($w[0],7,iconv( 'UTF-8','TIS-620',''),1,0,'C');
				$this->pdf->SetFont('angsa','U',16);
				$text_t = $home["HOME_NAME"];	
				switch ($home["HOME_TYPE_ID"]) {
					case '1':
						$text_t .= ' ห้องอาคารหมายเลข '.$home["HOME_NUMBER3"];
						break;
					case '2':
						$text_t .= ' '.count($secs).' ชั้น ชั้น '.$sec["HOME_SECTION_ORDER"].' จำนวน '.count($rooms).' ห้องอาคารหมายเลข '.$home["HOME_NUMBER3"];
						break;
					case '3':
						$text_t .= ' จำนวน '.count($rooms).' ห้องอาคารหมายเลข '.$home["HOME_NUMBER3"];
						break;
					case '4':
						$text_t .= ' ห้องอาคารหมายเลข '.$home["HOME_NUMBER3"];
						break;
					case '5':
						$text_t .= ' ห้องอาคารหมายเลข '.$home["HOME_NUMBER3"];
					break;
					case '6':
						$text_t .= ' ห้องอาคารหมายเลข '.$home["HOME_NUMBER3"];
					break;
					default:
						break;
				}
				$this->pdf->Cell(120,7,iconv( 'UTF-8','TIS-620',$text_t),1,0,'C');
				$this->pdf->SetFont('angsa','',14);	
				$this->pdf->Cell($w[5],7,iconv( 'UTF-8','TIS-620',''),1,0,'C');
				$this->pdf->Ln();

				foreach ($rooms as $key_room => $room) { 
					// for($i=0;$i<count($header);$i++)
					 $this->pdf->Cell($w[0],7,iconv( 'UTF-8','TIS-620',$key_room+1),1,0,'C');

					 if($home["HOME_TYPE_ID"] == 2 || $home["HOME_TYPE_ID"] == 3){
						$this->pdf->Cell($w[1],7,iconv( 'UTF-8','TIS-620',$room["ROOM_ADDRESS"]),1,0,'C');
						$this->pdf->Cell($w[2],7,iconv( 'UTF-8','TIS-620',$room["HOME_NUMBER"]),1,0,'C');
					}else if($home["HOME_TYPE_ID"] == 1 || $home["HOME_TYPE_ID"] == 4 || $home["HOME_TYPE_ID"] == 6){
						$this->pdf->Cell($w[1],7,iconv( 'UTF-8','TIS-620',$room["ROOM_ADDRESS2"]),1,0,'C');
						$this->pdf->Cell($w[2],7,iconv( 'UTF-8','TIS-620',$home["HOME_NUMBER3"]),1,0,'C');
					}else{
						$this->pdf->Cell($w[1],7,iconv( 'UTF-8','TIS-620',$room["ROOM_ADDRESS2"]),1,0,'C');
						$this->pdf->Cell($w[2],7,iconv( 'UTF-8','TIS-620',$room["HOME_NUMBER4"]),1,0,'C');
					}

					 if($room["ROOM_STATUS_ID"] != 2)
					 	$this->pdf->Cell($w[3],7,iconv( 'UTF-8','TIS-620',$room["ROOM_STATUS_NAME"]),1,0,'C');
					 else
					 	$this->pdf->Cell($w[3],7,iconv( 'UTF-8','TIS-620',$room["FIRST_NAME"]),1,0,'C');
					 $this->pdf->Cell($w[4],7,iconv( 'UTF-8','TIS-620',$room["OWNER"]),1,0,'C');
					 $this->pdf->Cell($w[5],7,iconv( 'UTF-8','TIS-620',explode(" ",$room["REFERENCE_DATE"])[0]),1,0,'C');
					 $this->pdf->Ln();  		
				} 
			}
			

		}


		$this->pdf->Output("report.pdf","F");

		$this->load->helper('download');

		$data = file_get_contents("report.pdf");
		$name = "รายงาน/pdf.pdf";

		force_download($name, $data); 
		//echo anchor('MyPDF/test.pdf', 'Download');
	}

	public function reportByRoom(){
		$this->load->library('pdf_nohead'); // 
		$this->pdf_nohead->fontpath = 'fonts/'; // Create folder fonts at Codeigniter
		$roomId = $this->path_variable;
		// $homeId = $this->input->get("home");
		$roomMap = $this->family_room_mappings_model->findLastFamilyByRoom($roomId);
		$room = $this->room_model->findByPk($roomMap["ROOM_ID"]);
		
		$this->pdf_nohead->SetMargins(20,10,20);
		$this->pdf_nohead->AliasNbPages();
		$this->pdf_nohead->AddPage();
		$this->pdf_nohead->AddFont('angsa','','angsa.php');
		$this->pdf_nohead->AddFont('angsa','B','angsab.php');
		$this->pdf_nohead->AddFont('angsa','I','angsai.php');
		$this->pdf_nohead->AddFont('angsa','U','angsaz.php');
		$this->pdf_nohead->SetFont('angsa','U',18);
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','รายงานบ้านพักอาศัย'),0,1,'C');
		$this->pdf_nohead->Ln();
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','อาคารหมายเลข '.$room["ROOM_ADDRESS"]),0,1,'C');


		$familyId = $roomMap["FAMILY_ID"];
		$family = $this->families_model->findByPk($familyId);
		$headFam = $this->person_model->findByPk($family["PERS_ID"]);
		$familyMembers = $this->family_members_model->findByColumns([$this->family_members_model->FAMILY_MEMBERS_FK_FAMILY,"IS_STAY"],[$family["FAMILY_ID"],true]);
		
		$this->pdf_nohead->SetFont('angsa','',14);
		$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620','1.'),0,1,'L');
		$this->pdf_nohead->Ln();
		$this->pdf_nohead->Image(base_url().$headFam["CURRENT"]["PICTURE_PATH"],90,null,30);
		$this->pdf_nohead->Ln();
		// $addr = $this->address_model->findDistricById($headFam["CURRENT"]["DISTRICT_ID_TYPE0"]);
		$pv=$this->address_model->get("provinces",$headFam["CURRENT"]["PROVINCE_ID_TYPE0"]);
		$ap=$this->address_model->get("amphures",$headFam["CURRENT"]["AMPHUR_ID_TYPE0"]);
		$dt=$this->address_model->get("districts",$headFam["CURRENT"]["DISTRICT_ID_TYPE0"]);
		$header = ["ยศ. ชื่อ-สกุลผู้ร่วมอาศัย" => $headFam["CURRENT"]["FIRST_NAME"]
					,"เพศ" => $headFam["CURRENT"]["GENDER"] =='M' ? "ชาย" : "หญิง"
					,"เลขประจำตัวประชาชน" => $headFam["CURRENT"]["PERS_N_ID"]
					,"ความสัมพันธ์" => "เจ้าบ้าน"
					,"วัน.เดือน.ปี เกิด" => $this->dateStrTh(explode(" ", $headFam["BIRTHDAY"])[0])
					,"สัญชาติ" => $headFam["CURRENT"]["NATIONALITY"]
					,"การศึกษา" => $headFam["CURRENT"]["EDUCATION"]
					,"อาชีพ" => $headFam["CURRENT"]["CAREER"]
					,"สังกัด โรงเรียน ชื่อหน่วนงาน" => $headFam["CURRENT"]["ACADEMY"]
					,"เบอร์โทรศัพท์ที่ทำงาน" => $headFam["CURRENT"]["PHONE_NBR"]
					,"เบอร์โทรศัพท์ส่วนตัว" => $headFam["CURRENT"]["MOBILE_NBR_1"]
					,"เข้าพักเมื่อ" =>$this->dateStrTh(explode(" ",  $roomMap["START_DATE"])[0])
					,"ออกเมื่อ (สาเหตุ)" => ""
					,"ภูมิลำเนาปัจจุบัน" => $headFam["CURRENT"]["ADDRESS_1_TYPE0"].' '.($dt != null ? 'ต.'.$dt["name_th"]:'').' '.($ap != null ? 'อ.'.$ap["name_th"]:'').' '.($pv != null ? 'จ.'.$pv["name_th"]:'').' '.($dt != null ? $dt["zip_code"]:'')
					,"ทะเบียนรถยนต์" => $headFam["CURRENT"]["CAR_NUMBER"]
					,"ทะเบียนรถจักยานยนต์" => $headFam["CURRENT"]["BIKER_NUMBER"]
					,"หมายเหตุ" => $headFam["CURRENT"]["REFERENCE"]
				];

		foreach ($header as $key => $value) {
		    $this->pdf_nohead->Cell(80,7,iconv( 'UTF-8','TIS-620',$key." : "),0,0,'R');
		    $this->pdf_nohead->Cell(100,7,iconv( 'UTF-8','TIS-620',$value),0,0,'L');
			$this->pdf_nohead->Ln();
		}
		$index=1;
		foreach ($familyMembers as $key => $m) {
			$person = $this->person_model->findByPk($m["PERS_ID"]);
			$familyMembers[$key]["PERSON"] = $person;
			$this->pdf_nohead->AddPage();
			$this->pdf_nohead->Cell(0,10,iconv( 'UTF-8','TIS-620',++$index.'.'),0,1,'L');
			$this->pdf_nohead->Ln();
			$this->pdf_nohead->Image(base_url().$person["CURRENT"]["PICTURE_PATH"],90,null,30);
			$this->pdf_nohead->Ln();
			// $addr = $this->address_model->findDistricById($person["CURRENT"]["DISTRICT_ID_TYPE0"]);
			$pv=$this->address_model->get("provinces",$person["CURRENT"]["PROVINCE_ID_TYPE0"]);
			$ap=$this->address_model->get("amphures",$person["CURRENT"]["AMPHUR_ID_TYPE0"]);
			$dt=$this->address_model->get("districts",$person["CURRENT"]["DISTRICT_ID_TYPE0"]);
			$detail = ["ยศ. ชื่อ-สกุลผู้ร่วมอาศัย" => $person["CURRENT"]["FIRST_NAME"]
					,"เพศ" => $person["CURRENT"]["GENDER"] =='M' ? "ชาย" : "หญิง"
					,"เลขประจำตัวประชาชน" => $person["CURRENT"]["PERS_N_ID"]
					,"ความสัมพันธ์" => $m["FAMILY_MEMBER_STATUS"]
					,"วัน.เดือน.ปี เกิด" => $this->dateStrTh(explode(" ", $person["BIRTHDAY"])[0])
					,"สัญชาติ" => $person["CURRENT"]["NATIONALITY"]
					,"การศึกษา" => $person["CURRENT"]["EDUCATION"]
					,"อาชีพ" => $person["CURRENT"]["CAREER"]
					,"สังกัด โรงเรียน ชื่อหน่วนงาน" => $person["CURRENT"]["ACADEMY"]
					,"เบอร์โทรศัพท์ที่ทำงาน" => $person["CURRENT"]["PHONE_NBR"]
					,"เบอร์โทรศัพท์ส่วนตัว" => $person["CURRENT"]["MOBILE_NBR_1"]
					,"เข้าพักเมื่อ" => $this->dateStrTh(explode(" ", $m["START_DATE"])[0])
					,"ออกเมื่อ (สาเหตุ)" => ""
					,"ภูมิลำเนาปัจจุบัน" => $person["CURRENT"]["ADDRESS_1_TYPE0"].' '.($dt != null ? 'ต.'.$dt["name_th"]:'').' '.($ap != null ? 'อ.'.$ap["name_th"]:'').' '.($pv != null ? 'จ.'.$pv["name_th"]:'').' '.($dt != null ? $dt["zip_code"]:'')
					,"ทะเบียนรถยนต์" => $person["CURRENT"]["CAR_NUMBER"]
					,"ทะเบียนรถจักยานยนต์" => $person["CURRENT"]["BIKER_NUMBER"]
					,"หมายเหตุ" => $person["CURRENT"]["REFERENCE"]
				];
			foreach ($detail as $key => $value) {
			    $this->pdf_nohead->Cell(80,7,iconv( 'UTF-8','TIS-620',$key." : "),0,0,'R');
			    $this->pdf_nohead->Cell(100,7,iconv( 'UTF-8','TIS-620',$value),0,0,'L');
				$this->pdf_nohead->Ln();
			}
		}
		$family["HEAD_FAM"] = $headFam;
		$family["MEMBERS"] = $familyMembers;

		$roomMap["ROOM"] = $room;
		$roomMap["FAMILY"] = $family;
		 // $this->return_json($roomMap);

		$this->pdf_nohead->Output("report.pdf","F");

		$this->load->helper('download');

		$data = file_get_contents("report.pdf");
		$name = "รายงาน/pdf.pdf";

		force_download($name, $data); 
		//echo anchor('MyPDF/test.pdf', 'Download');
	}

	public function getDot($value=20){
		$d = "";
		for ($i=0; $i <$value ; $i++) { 
			$d .= ".";
		}
		return $d;
	}
	public function dateStrTh($date){
		if(empty($date)) return 'ไม่ระบุ';
		$month_th= [
			    "มกราคม",
			    "กุมภาพันธ์",
			    "มีนาคม",
			    "เมษายน",
			    "พฤษภาคม",
			    "มิถุนายน",
			    "กรกฎาคม",
			    "สิงหาคม",
			    "กันยายน",
			    "ตุลาคม",
			    "พฤศจิกายน",
			    "ธันวาคม"
			];
		$d = new DateTime($date, new DateTimeZone('Asia/Bangkok')); 
		return intval($d->format('d'))." ".$month_th[intval($d->format('m'))]." ".$d->format('Y');
	}
}
