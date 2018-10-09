<?php
class Pers_Account_Model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->TABLE = "PERS_ACCOUNTS";
    $this->USERNAME = "USERNAME";
    $this->PASSWORD = "PASSWORD";
    // $this->TABLE_PERSON_CURRENT = "PERSON_CURRENTS";
    $this->PK = "PERS_ID";
    $this->load->model("general_model");
    // $this->load->model("person_current_model");
  }

  public function _new($ID=NULL){
    return array(
      "PERS_ID"=>$ID,
      "TYPE_ID"=>NULL,
      "BIRTHDAY"=>NULL,
      "CREATE_DATE"=>NULL,
      "LAST_UPDATE"=>NULL
    );
  }

  // ADD
  public function merge($data,$is_add)
  {
      if($is_add){
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
      $row = $this->general_model->findByPk($this->TABLE,$this->PK,$ID);
      // $row["CURRENT"] = $this->person_current_model->findByPk($ID);
      return $row;
  }
  public function findByColumn($field='',$value=''){
      return $this->general_model->findByColumn($this->TABLE,$field,$value);
  }
  public function findByColumns($fields=[],$values=[]){
      return $this->general_model->findByColumns($this->TABLE,$fields,$values);      
  }

  
}?>


