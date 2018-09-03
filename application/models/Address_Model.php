<?php
class Address_Model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->TABLE = "provinces";
    $this->PK = "id";
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

  public function findDistric($key){
      // $this->select()->from()->join()->join()->where()->order_by()->get();
      $sql = "SELECT d.id as d_id,d.zip_code,d.name_th as d_name,a.id as a_id,a.name_th as a_name,p.id as p_id,p.name_th as p_name 
              FROM districts d  
              INNER join amphures a on a.id = d.amphure_id
              INNER join provinces p on p.id = a.province_id ORDER BY p.name_th ";
              // WHERE d.name_th like '".$key."%' ORDER BY d.name_th";
      $q = $this->db->query($sql);
      return $q->result_array();
  }
  public function findDistricById($id){
      $sql = "SELECT d.id as d_id,d.zip_code,d.name_th as d_name,a.id as a_id,a.name_th as a_name,p.id as p_id,p.name_th as p_name 
              FROM districts d  
              INNER join amphures a on a.id = d.amphure_id
              INNER join provinces p on p.id = a.province_id WHERE d.id='".$id."' ORDER BY p.name_th ";
      $q = $this->db->query($sql);
      return $q->row_array();
  }
}?>


