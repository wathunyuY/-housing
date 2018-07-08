<?php
class General_Model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  // ADD
  public function addData($data,$table)
  {
      $this->db->insert($table, $data);
      $id = $this->db->insert_id();
      return $id;
  }

  public function updateData($data='',$table='',$reffield='',$refvalue='')
  {
      $this->db->where($reffield,$refvalue);
      $this->db->update($table,$data);
  }

  public function deleteData($table='',$reffield='',$refvalue=''){
      $this->db->where($reffield,$refvalue);
      $this->db->delete($table);
  }

  public function findAll($table,$PK){
    return $this->db->select()->from($table)->order_by($PK)->get()->result_array();
  }

  public function findByPk($table='',$field='',$value=''){
      $sql = "
          SELECT *
          FROM ".$table."
          WHERE  ".$field." = '".$value."' "; 
      $query = $this->db->query($sql);
      if(0 < count($query->result_array()))
        return $query->row_array();
      else return NULL;
  }
  public function findByColumn($table='',$field='',$value=''){
      $sql = "
          SELECT *
          FROM ".$table."
          WHERE  ".$field." = '".$value."' "; 
      $query = $this->db->query($sql);
      return $query->result_array();
  }
  public function findByColumns($table='',$fields=[],$values=[]){
      if(0 === count($fields) || 0 === count($values) || count($values) !== count($fields)) return NULL;
      $condition = " 1=1 ";
      foreach ($fields as $key => $f) {
        $condition.= " and ".$f." = '".$values[$key]."'";
      }
      $sql = "
          SELECT *
          FROM ".$table."
          WHERE  ".$condition;
      $query = $this->db->query($sql);
      return $query->result_array();
  }

  
}?>


