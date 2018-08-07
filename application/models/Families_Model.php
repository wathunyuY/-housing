<?php
class Families_Model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->TABLE = "FAMILIES";
    $this->PK = "FAMILY_ID";
    $this->load->model("general_model");
    $this->load->model("family_members_model");
    $this->load->model("person_model");
  }

  public function _new($ID =NULL){
    return array(
      "FAMILY_ID"=>$ID,
      "FAMILY_NAME"=>NULL,
      "PERS_ID"=>NULL
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

  public function members($ID){
      $members = $this->family_members_model->findByFamily($ID);
      if(count($members)){
          foreach ($members as $key => $m) {
             $person = $this->person_model->findByPk($m["PERS_ID"]);
             $members[$key]["PERSON"] = $person;
          }
      } 
      return $members;
  }
}?>


