<?php
class Home_Model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->TABLE = "Homes";
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
      return $this->general_model->findByColumn($this->TABLE,$fields,$values);      
  }

  public function haveFamily($homeId){
    $sql = "SELECT * FROM homes a 
            INNER JOIN home_sections b on b.HOME_ID = a.HOME_ID
            INNER JOIN home_rooms c ON c.HOME_SECTION_ID = b.HOME_SECTION_ID
            WHERE ROOM_STATUS_ID <> 1 AND a.HOME_ID = ".$homeId;
    return $this->db->query($sql)->num_rows() > 0;
  }
  
}?>


