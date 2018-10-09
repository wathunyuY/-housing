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

  public function search($ownerId,$key='',$provinceId=0,$amphurId=0,$districtId=0){

    $p = $provinceId ==0 ? '': ' OR pc.PROVINCE_ID_TYPE0 = '.$provinceId;
    $a = $amphurId ==0 ? '': ' AND pc.AMPHUR_ID_TYPE0 = '.$amphurId;
    $d = $districtId ==0 ? '': ' AND pc.DISTRICT_ID_TYPE0 = '.$districtId;
    $p2 = $provinceId ==0 ? '': ' OR pc2.PROVINCE_ID_TYPE0 = '.$provinceId;
    $a2 = $amphurId ==0 ? '': ' AND pc2.AMPHUR_ID_TYPE0 = '.$amphurId;
    $d2 = $districtId ==0 ? '': ' AND pc2.DISTRICT_ID_TYPE0 = '.$districtId;

    $key = $key == '' || $key == null ? '!@#$%^' : '%'.$key.'%';

    $sql = 'SELECT hr.ROOM_ID,hr.ROOM_NAME,hr.ROOM_SEQ,hr.ROOM_ORDER,hr.ROOM_ADDRESS,hr.ROOM_SUB_ADDRESS
            ,rs.HOME_SECTION_ID,rs.HOME_SECTION_ORDER,rs.HOME_SECTION_NAME
            ,h.HOME_ID,HOME_ADDR,h.HOME_NUMBER,h.HOME_SUB_NUMBER,HOME_NAME,HOME_SUB_NUMBER_SEQ
            ,htt.HOME_TYPE_ID,htt.HOME_TYPE_NAME
            ,rst.ROOM_STATUS_ID,rst.ROOM_STATUS_NAME
            ,own.OWNER_GROUP_ID,own.OWNER_GROUP_NAME,own.OWNER_GROUP_DESCR
            ,IF(rst.ROOM_STATUS_ID = 2,pc.FIRST_NAME,CONCAT("ไม่มี  :  ",rst.ROOM_STATUS_NAME)) as FIRST_NAME
            ,(SELECT COUNT(*)+1 FROM FAMILY_MEMBERS fm WHERE fm.FAMILY_ID = f.FAMILY_ID AND fm.IS_STAY = true) as MEMBER_COUNT
            ,GROUP_CONCAT(pc2.FIRST_NAME) as MEMBER_FIRST_NAMES
            FROM HOME_ROOMS hr
            INNER JOIN HOME_SECTIONS rs ON hr.HOME_SECTION_ID = rs.HOME_SECTION_ID
            INNER JOIN HOMES h ON h.HOME_ID = rs.HOME_ID
            INNER JOIN OWNER_GROUP_TBLS own ON own.OWNER_GROUP_ID = h.OWNER_GROUP_ID
            INNER JOIN HOME_TYPE_TBLS htt ON htt.HOME_TYPE_ID = h.HOME_TYPE_ID
            INNER JOIN ROOM_STATUS_TBLS rst ON rst.ROOM_STATUS_ID = hr.ROOM_STATUS_ID
            INNER JOIN FAMILY_ROOM_MAPPINGS frm ON frm.ROOM_ID = hr.ROOM_ID
            INNER JOIN FAMILIES f ON f.FAMILY_ID = frm.FAMILY_ID 
            INNER JOIN PERSONS p ON p.PERS_ID = f.PERS_ID
            INNER JOIN PERSON_CURRENTS pc ON pc.PERS_ID = p.PERS_ID
            LEFT JOIN FAMILY_MEMBERS fmb ON fmb.FAMILY_ID = f.FAMILY_ID AND fmb.IS_STAY = true
            LEFT JOIN PERSONS p2 ON p2.PERS_ID = fmb.PERS_ID
            LEFT JOIN PERSON_CURRENTS pc2 ON pc2.PERS_ID = p2.PERS_ID
            WHERE rst.ROOM_STATUS_ID = 2
            AND ( 
                  pc.FIRST_NAME like "'.$key.'" 
                  OR h.HOME_NAME like "'.$key.'"
                  OR CONCAT(h.HOME_NUMBER,"/",h.HOME_SUB_NUMBER) like "'.$key.'"
                  OR CONCAT(hr.ROOM_ADDRESS,"/",hr.ROOM_SUB_ADDRESS) like "'.$key.'"
                  OR pc.PERS_N_ID like "'.$key.'"
                  OR pc.PERS_NICKNAME like "'.$key.'"
                  OR pc.CAREER like "'.$key.'"
                  OR pc.REFERENCE like "'.$key.'"
                  OR pc.EDUCATION like "'.$key.'"
                  OR pc.ACADEMY like "'.$key.'"
                  OR pc.PHONE_NBR like "'.$key.'"
                  OR pc.MOBILE_NBR_1 like "'.$key.'"
                  OR pc.CAR_NUMBER like "'.$key.'"
                  OR pc.BIKER_NUMBER like "'.$key.'"
                  '.$p.$a.$d.'


                  OR pc2.FIRST_NAME like "'.$key.'"
                  OR pc2.PERS_N_ID like "'.$key.'"
                  OR pc2.PERS_NICKNAME like "'.$key.'"
                  OR pc2.CAREER like "'.$key.'"
                  OR pc2.REFERENCE like "'.$key.'"
                  OR pc2.EDUCATION like "'.$key.'"
                  OR pc2.ACADEMY like "'.$key.'"
                  OR pc2.PHONE_NBR like "'.$key.'"
                  OR pc2.MOBILE_NBR_1 like "'.$key.'"
                  OR pc2.CAR_NUMBER like "'.$key.'"
                  OR pc2.BIKER_NUMBER like "'.$key.'"
                  '.$p2.$a2.$d2.'
            )
            AND own.OWNER_GROUP_ID = '.$ownerId.' GROUP BY hr.ROOM_ID ORDER BY hr.ROOM_ID';
            // return ["a"=>$sql];
    return $this->db->query($sql)->result_array(); 
  }
  
}?>


