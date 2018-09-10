<?php
class Room_Model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->TABLE = "HOME_ROOMS";
    $this->PK = "ROOM_ID";
    $this->load->model("general_model");
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

  public function haveFamily($roomId){
    $sql = "SELECT * FROM  HOME_ROOMS c 
            WHERE ROOM_STATUS_ID <> 1 AND c.ROOM_ID = ".$roomId;
    return $this->db->query($sql)->num_rows() > 0;
  }

  public function search($ownerId,$key="!@#$%"){
    $sql = 'SELECT hr.ROOM_ID,hr.ROOM_NAME,hr.ROOM_SEQ,hr.ROOM_ORDER,hr.ROOM_ADDRESS,hr.ROOM_SUB_ADDRESS
            ,rs.HOME_SECTION_ID,rs.HOME_SECTION_ORDER,rs.HOME_SECTION_NAME
            ,h.HOME_ID,HOME_ADDR,h.HOME_NUMBER,h.HOME_SUB_NUMBER,HOME_NAME
            ,htt.HOME_TYPE_ID,htt.HOME_TYPE_NAME
            ,rst.ROOM_STATUS_ID,rst.ROOM_STATUS_NAME
            ,own.OWNER_GROUP_ID,own.OWNER_GROUP_NAME,own.OWNER_GROUP_DESCR
            ,IF(rst.ROOM_STATUS_ID = 2,pc.FIRST_NAME,CONCAT("ไม่มี  :  ",rst.ROOM_STATUS_NAME)) as FIRST_NAME
            ,(SELECT COUNT(*)+1 FROM FAMILY_MEMBERS fm WHERE fm.FAMILY_ID = f.FAMILY_ID AND fm.IS_STAY = true) as MEMBER_COUNT
            FROM HOME_ROOMS hr
            INNER JOIN HOME_SECTIONS rs ON hr.HOME_SECTION_ID = rs.HOME_SECTION_ID
            INNER JOIN HOMES h ON h.HOME_ID = rs.HOME_ID
            INNER JOIN OWNER_GROUP_TBLS own ON own.OWNER_GROUP_ID = h.OWNER_GROUP_ID
            INNER JOIN HOME_TYPE_TBLS htt ON htt.HOME_TYPE_ID = h.HOME_TYPE_ID
            INNER JOIN ROOM_STATUS_TBLS rst ON rst.ROOM_STATUS_ID = hr.ROOM_STATUS_ID
            LEFT JOIN FAMILY_ROOM_MAPPINGS frm ON frm.ROOM_ID = hr.ROOM_ID
            LEFT JOIN FAMILIES f ON f.FAMILY_ID = frm.FAMILY_ID 
            LEFT JOIN PERSON_CURRENTS pc ON pc.PERS_ID = f.PERS_ID
            WHERE rst.ROOM_STATUS_ID = 2 
            AND (CONCAT(pc.FIRST_NAME," ",pc.PERS_N_ID," ",hr.ROOM_ADDRESS,"/",hr.ROOM_SUB_ADDRESS,pc.PERS_NICKNAME,pc.CAR_NUMBER,pc.BIKER_NUMBER,pc.CAREER) like "%'.$key.'%") 
            AND own.OWNER_GROUP_ID = '.$ownerId;
    return $this->db->query($sql)->result_array(); 
  }
  
}?>


