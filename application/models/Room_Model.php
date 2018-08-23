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
      return $this->general_model->findByColumn($this->TABLE,$fields,$values);      
  }

  public function haveFamily($roomId){
    $sql = "SELECT * FROM  home_rooms c 
            WHERE ROOM_STATUS_ID <> 1 AND c.ROOM_ID = ".$roomId;
    return $this->db->query($sql)->num_rows() > 0;
  }
  
}?>


