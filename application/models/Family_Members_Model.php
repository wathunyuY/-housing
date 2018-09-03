<?php
class Family_Members_Model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->TABLE = "FAMILY_MEMBERS";
    $this->PK = "FAMILY_MEMBER_ID";
    $this->FAMILY_MEMBERS_FK_FAMILY = "FAMILY_ID";
    $this->load->model("general_model");
  }

  public function _new($ID=NULL){
    return array(
      "FAMILY_MEMBER_ID"=>$ID,
      "FAMILY_ID"=>NULL,
      "PERS_ID"=>NULL,
      "FAMILY_MEMBER_STATUS"=>NULL,
      "IS_STAY"=>NULL,
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
      return $this->general_model->findByColumns($this->TABLE,$fields,$values);      
  }

  public function findByFamily($ID){
    return $this->findByColumns([$this->FAMILY_MEMBERS_FK_FAMILY,"IS_STAY"],[$ID,TRUE]);
  }
  
}?>


