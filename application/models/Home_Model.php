<?php
class Home_Model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->TABLE = "HOMES";
    $this->PK = "HOME_ID";
    $this->load->model("general_model");
    $this->load->model("home_section_model");
    $this->load->model("room_model");
  }

  // ADD
  public function merge($data)
  {
      if(NULL === $data[$this->PK]){
        $id = $this->general_model->addData($data,$this->TABLE);
        return $this->findByPk($id);
      }
      else {
        $this->general_model->updateData($data,$this->TABLE,$this->PK,$data[$this->PK]);
        return $this->findByPk($data[$this->PK]);
      }
  }
  public function delete($ID){
      $this->general_model->deleteData($this->TABLE,$this->PK,$ID);
  }

  public function findAll(){
    return $this->general_model->findAll($this->TABLE,$this->PK);
  }

  public function findByPk($ID){
      return $this->general_model->findByPk($this->TABLE,$this->PK,$ID);
  }
  public function findByColumn($field='',$value=''){
      return $this->general_model->findByColumn($this->TABLE,$field,$value);
  }
  public function findByColumns($fields=[],$values=[]){
      return $this->general_model->findByColumns($this->TABLE,$fields,$values);      
  }

  public function haveFamily($homeId){
    $sql = "SELECT * FROM HOMES a 
            INNER JOIN HOME_SECTIONS b on b.HOME_ID = a.HOME_ID
            INNER JOIN HOME_ROOMS c ON c.HOME_SECTION_ID = b.HOME_SECTION_ID
            WHERE ROOM_STATUS_ID = 2 AND a.HOME_ID = ".$homeId;
    return $this->db->query($sql)->num_rows() > 0;
  }

  public function findHomeReport($owId,$homeId =NULL){
    $sql = 'SELECT a.HOME_ID,b.HOME_SECTION_ID,c.ROOM_ID ,b.HOME_SECTION_ORDER+1 as HOME_SECTION_ORDER,a.HOME_TYPE_ID
            ,CONCAT(a.HOME_ADDR,IF(c.ROOM_SUB_ADDRESS > 0,CONCAT("/",c.ROOM_SUB_ADDRESS),"")) as ROOM_ADDRESS
            ,CONCAT(a.HOME_NUMBER,"/",a.HOME_SUB_NUMBER,"/",b.HOME_SECTION_ORDER+1," (",c.ROOM_SEQ,")") as HOME_NUMBER
            ,CONCAT(a.HOME_NUMBER,"/",a.HOME_SUB_NUMBER,"/",b.HOME_SECTION_ORDER+1) as HOME_NUMBER2
            ,CONCAT(a.HOME_NUMBER,"/",a.HOME_SUB_NUMBER) as HOME_NUMBER3
            ,cc.ROOM_STATUS_ID,cc.ROOM_STATUS_NAME
            ,a.HOME_NAME
            ,b.HOME_SECTION_NAME
            ,c.ROOM_NAME
            ,f.FIRST_NAME
            ,f.REFERENCE 
            ,ow.OWNER_GROUP_DESCR
            ,if(owp.OWNER_GROUP_DESCR is null ,"",owp.OWNER_GROUP_DESCR) as OWNER_GROUP_DESCR_2
            ,d.START_DATE as REFERENCE_DATE
          FROM HOMES a  
          INNER JOIN OWNER_GROUP_TBLS ow on ow.OWNER_GROUP_ID = a.OWNER_GROUP_ID  
          INNER JOIN HOME_SECTIONS b on b.HOME_ID = a.HOME_ID
          INNER JOIN HOME_ROOMS c on c.HOME_SECTION_ID = b.HOME_SECTION_ID
          INNER JOIN ROOM_STATUS_TBLS cc on cc.ROOM_STATUS_ID = c.ROOM_STATUS_ID
          LEFT JOIN FAMILY_ROOM_MAPPINGS d ON d.ROOM_ID = c.ROOM_ID AND d.END_DATE IS NULL
          LEFT JOIN FAMILIES e on e.FAMILY_ID = d.FAMILY_ID
          LEFT JOIN PERSON_CURRENTS f ON f.PERS_ID = e.PERS_ID
          LEFT JOIN OWNER_GROUP_TBLS owp on owp.OWNER_GROUP_ID = f.OWNER_GROUP_ID
          WHERE ow.OWNER_GROUP_ID ='.$owId;
    if(NULL != $homeId) $sql.=' AND a.HOME_ID='.$homeId;
    $sql .=' ORDER BY a.HOME_ID,b.HOME_SECTION_ORDER,c.ROOM_SUB_ADDRESS';
    $rs = $this->db->query($sql)->result_array();
    $hId = -1;
    $sId = -1;
    $index = -1;
    foreach ($rs as $key => $h) {
      if($h["HOME_ID"] != $hId){
        $home["OWNER_GROUP_DESCR"] = $h["OWNER_GROUP_DESCR"];
        $home["HOME_NAME"] = $h["HOME_NAME"];
        $home["HOME_NUMBER3"] = $h["HOME_NUMBER3"];
        $home["HOME_TYPE_ID"] = $h["HOME_TYPE_ID"];
        $home["SECS"] = [];
        $homes[++$index] = $home;
        $hId = $h["HOME_ID"] ;
      }
      if($sId != $h["HOME_SECTION_ID"]){
        $sec["HOME_NUMBER"] = $h["HOME_NUMBER2"];
        $sec["HOME_SECTION_ORDER"] = $h["HOME_SECTION_ORDER"];
        $sec["ROOMS"] = [];
        array_push($homes[$index]["SECS"], $sec);
        $sId = $h["HOME_SECTION_ID"];
      }
      $room["ROOM_STATUS_ID"] = $h["ROOM_STATUS_ID"];
      $room["ROOM_STATUS_NAME"] = $h["ROOM_STATUS_NAME"];
      $room["ROOM_ADDRESS"] = $h["ROOM_ADDRESS"];
      $room["HOME_NUMBER"] = $h["HOME_NUMBER"];
      $room["FIRST_NAME"] = $h["FIRST_NAME"];
      $room["REFERENCE"] = $h["REFERENCE"];
      $room["OWNER"] = $h["OWNER_GROUP_DESCR_2"];
      $room["REFERENCE_DATE"] = $h["REFERENCE_DATE"];
      array_push($homes[$index]["SECS"][count($homes[$index]["SECS"])-1]["ROOMS"],$room);
    }
    return $homes;
  }

  public function findRoomByStatusAndOwnerId($status,$ownerId){
    $sql = 'SELECT hr.ROOM_ID,hr.ROOM_NAME,hr.ROOM_SEQ,hr.ROOM_ORDER,hr.ROOM_ADDRESS,hr.ROOM_SUB_ADDRESS
            ,rs.HOME_SECTION_ID,rs.HOME_SECTION_ORDER,rs.HOME_SECTION_NAME
            ,h.HOME_ID,HOME_ADDR,h.HOME_NUMBER,h.HOME_SUB_NUMBER,HOME_NAME
            ,htt.HOME_TYPE_ID,htt.HOME_TYPE_NAME
            ,rst.ROOM_STATUS_ID,rst.ROOM_STATUS_NAME
            ,own.OWNER_GROUP_ID,own.OWNER_GROUP_NAME,own.OWNER_GROUP_DESCR
            ,IF(rst.ROOM_STATUS_ID = 2,pc.FIRST_NAME,CONCAT("ไม่มี : ",rst.ROOM_STATUS_NAME)) as FIRST_NAME
            FROM HOME_ROOMS hr
            INNER JOIN HOME_SECTIONS rs ON hr.HOME_SECTION_ID = rs.HOME_SECTION_ID
            INNER JOIN HOMES h ON h.HOME_ID = rs.HOME_ID
            INNER JOIN OWNER_GROUP_TBLS own ON own.OWNER_GROUP_ID = h.OWNER_GROUP_ID
            INNER JOIN HOME_TYPE_TBLS htt ON htt.HOME_TYPE_ID = h.HOME_TYPE_ID
            INNER JOIN ROOM_STATUS_TBLS rst ON rst.ROOM_STATUS_ID = hr.ROOM_STATUS_ID
            LEFT JOIN FAMILY_ROOM_MAPPINGS frm ON frm.ROOM_ID = hr.ROOM_ID
            LEFT JOIN FAMILIES f ON f.FAMILY_ID = frm.FAMILY_ID 
            LEFT JOIN PERSON_CURRENTS pc ON pc.PERS_ID = f.PERS_ID
            WHERE rst.ROOM_STATUS_ID = '.$status.' AND own.OWNER_GROUP_ID = '.$ownerId.' ORDER BY h.HOME_ID,rs.HOME_SECTION_ID,hr.ROOM_ID';
    $tbls = $this->db->query($sql)->result_array();
    return $tbls;
  }
  public function findRoomByStatusAndHomeId($status,$homeId){
    $sql = 'SELECT hr.ROOM_ID,hr.ROOM_NAME,hr.ROOM_SEQ,hr.ROOM_ORDER,hr.ROOM_ADDRESS,hr.ROOM_SUB_ADDRESS
            ,rs.HOME_SECTION_ID,rs.HOME_SECTION_ORDER,rs.HOME_SECTION_NAME
            ,h.HOME_ID,HOME_ADDR,h.HOME_NUMBER,h.HOME_SUB_NUMBER,HOME_NAME
            ,htt.HOME_TYPE_ID,htt.HOME_TYPE_NAME
            ,rst.ROOM_STATUS_ID,rst.ROOM_STATUS_NAME
            ,own.OWNER_GROUP_ID,own.OWNER_GROUP_NAME,own.OWNER_GROUP_DESCR
            ,IF(rst.ROOM_STATUS_ID = 2,pc.FIRST_NAME,CONCAT("ไม่มี : ",rst.ROOM_STATUS_NAME)) as FIRST_NAME
            FROM HOME_ROOMS hr
            INNER JOIN HOME_SECTIONS rs ON hr.HOME_SECTION_ID = rs.HOME_SECTION_ID
            INNER JOIN HOMES h ON h.HOME_ID = rs.HOME_ID
            INNER JOIN OWNER_GROUP_TBLS own ON own.OWNER_GROUP_ID = h.OWNER_GROUP_ID
            INNER JOIN HOME_TYPE_TBLS htt ON htt.HOME_TYPE_ID = h.HOME_TYPE_ID
            INNER JOIN ROOM_STATUS_TBLS rst ON rst.ROOM_STATUS_ID = hr.ROOM_STATUS_ID
            LEFT JOIN FAMILY_ROOM_MAPPINGS frm ON frm.ROOM_ID = hr.ROOM_ID
            LEFT JOIN FAMILIES f ON f.FAMILY_ID = frm.FAMILY_ID 
            LEFT JOIN PERSON_CURRENTS pc ON pc.PERS_ID = f.PERS_ID
            WHERE rst.ROOM_STATUS_ID = '.$status.' AND h.HOME_ID = '.$homeId.' ORDER BY h.HOME_ID,rs.HOME_SECTION_ID,hr.ROOM_ID';
    $tbls = $this->db->query($sql)->result_array();
    return $tbls;
  }
  public function findRoomByStatusAndSectionId($status,$sectionId){
    $sql = 'SELECT hr.ROOM_ID,hr.ROOM_NAME,hr.ROOM_SEQ,hr.ROOM_ORDER,hr.ROOM_ADDRESS,hr.ROOM_SUB_ADDRESS
            ,rs.HOME_SECTION_ID,rs.HOME_SECTION_ORDER,rs.HOME_SECTION_NAME
            ,h.HOME_ID,HOME_ADDR,h.HOME_NUMBER,h.HOME_SUB_NUMBER,HOME_NAME
            ,htt.HOME_TYPE_ID,htt.HOME_TYPE_NAME
            ,rst.ROOM_STATUS_ID,rst.ROOM_STATUS_NAME
            ,own.OWNER_GROUP_ID,own.OWNER_GROUP_NAME,own.OWNER_GROUP_DESCR
            ,IF(rst.ROOM_STATUS_ID = 2,pc.FIRST_NAME,CONCAT("ไม่มี : ",rst.ROOM_STATUS_NAME)) as FIRST_NAME
            FROM HOME_ROOMS hr
            INNER JOIN HOME_SECTIONS rs ON hr.HOME_SECTION_ID = rs.HOME_SECTION_ID
            INNER JOIN HOMES h ON h.HOME_ID = rs.HOME_ID
            INNER JOIN OWNER_GROUP_TBLS own ON own.OWNER_GROUP_ID = h.OWNER_GROUP_ID
            INNER JOIN HOME_TYPE_TBLS htt ON htt.HOME_TYPE_ID = h.HOME_TYPE_ID
            INNER JOIN ROOM_STATUS_TBLS rst ON rst.ROOM_STATUS_ID = hr.ROOM_STATUS_ID
            LEFT JOIN FAMILY_ROOM_MAPPINGS frm ON frm.ROOM_ID = hr.ROOM_ID
            LEFT JOIN FAMILIES f ON f.FAMILY_ID = frm.FAMILY_ID 
            LEFT JOIN PERSON_CURRENTS pc ON pc.PERS_ID = f.PERS_ID
            WHERE rst.ROOM_STATUS_ID = '.$status.' AND rs.HOME_SECTION_ID = '.$sectionId.' ORDER BY h.HOME_ID,rs.HOME_SECTION_ID,hr.ROOM_ID';
    $tbls = $this->db->query($sql)->result_array();
    return $tbls;
  }
}?>


