<?php
class Family_Room_Mappings_Model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->TABLE = "FAMILY_ROOM_MAPPINGS";
    $this->PK = "FAMILY_ROOM_MAPPING_ID";
    $this->FK_ROOM = "ROOM_ID";
    $this->FK_FAMILY = "FAMILY_ID";
    $this->load->model("general_model");
    $this->load->model("families_model");
    $this->load->model("room_model");
    $this->load->model("person_model");
  }

  public function _new($ID =NULL){
    return array(
      "FAMILY_ROOM_MAPPING_ID"=>$ID,
      "ROOM_ID"=>NULL,
      "FAMILY_ID"=>NULL,
      "START_DATE"=>NULL,
      "END_DATE"=>NULL
    );
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
      return $this->general_model->findByColumn($this->TABLE,$fields,$values);      
  }

  public function findByRoom($roomId){
    $tbl = $this->db->select()->from($this->TABLE)->where("ROOM_ID",$roomId)->where("END_DATE",NULL)->get();
    $row = $tbl->row_array();
    if(isset($row)){
      $family = $this->families_model->findByPk($row["FAMILY_ID"]);
      $members = $this->families_model->members($family["FAMILY_ID"]);
      $headFam = $this->person_model->findByPk($family["PERS_ID"]);
      $room = $this->room_model->findByPk($row["ROOM_ID"]);
      $row["FAMILY"] = $family;
      $row["FAMILY"]["MEMBERS"] = $members;
      $row["FAMILY"]["PERSON"] = $headFam;
      $row["ROOM"] = $room;
      return $row;
    }else return NULL;
  }
  public function findLastFamily($familyId){
    $tbl = $this->db->select("*")->from($this->TABLE)->where($this->FK_FAMILY,$familyId)->where("END_DATE",null)->get();
    if($tbl->num_rows() > 0) return $tbl->row_array();
    else return null;
  }
  
}?>


