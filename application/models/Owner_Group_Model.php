<?php
class Owner_Group_Model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->TABLE = "OWNER_GROUP_TBLS";
    $this->PK = "OWNER_GROUP_ID";
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

  public function getAgeRankByOwnerId($id){
    $sql = "SELECT SUM(_12) as _12 ,SUM(_12_20) as _12_20 ,SUM(_20_40) as _20_40,SUM(_40_60) as _40_60,SUM(_60) as _60 FROM(
              SELECT 
                sum(if(age < 12,1, 0)) as _12 
                ,sum( if(age >=12 AND age <20 ,1,0) ) _12_20
                ,sum( if(age >=20 AND age <40 ,1,0) ) _20_40
                ,sum( if(age >=40 AND age <60 ,1,0) ) _40_60
                ,sum( if(age >=60 ,1,0) ) _60
              FROM(
                SELECT p.PERS_ID,p.BIRTHDAY, YEAR(CURRENT_TIMESTAMP) - YEAR(p.BIRTHDAY) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(p.BIRTHDAY, 5)) as age 
                FROM PERSONS p
                INNER JOIN FAMILY_MEMBERS fm ON fm.PERS_ID = p.PERS_ID
                INNER JOIN FAMILIES f ON f.FAMILY_ID = fm.FAMILY_ID
                INNER JOIN FAMILY_ROOM_MAPPINGS fp ON fp.FAMILY_ID = fm.FAMILY_ID
                INNER JOIN HOME_ROOMS hr ON hr.ROOM_ID = fp.ROOM_ID
                WHERE fm.IS_STAY = TRUE AND hr.OWNER_GROUP_ID = ".$id."
              ) as _members
              UNION
              SELECT 
                sum(if(age < 12,1, 0)) as _12 
                ,sum( if(age >=12 AND age <20 ,1,0) ) _12_20
                ,sum( if(age >=20 AND age <40 ,1,0) ) _20_40
                ,sum( if(age >=40 AND age <60 ,1,0) ) _40_60
                ,sum( if(age >=60 ,1,0) ) _60
              FROM(
                SELECT p.PERS_ID,p.BIRTHDAY, YEAR(CURRENT_TIMESTAMP) - YEAR(p.BIRTHDAY) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(p.BIRTHDAY, 5)) as age 
                FROM PERSONS p
                INNER JOIN FAMILIES fm ON fm.PERS_ID = p.PERS_ID 
                INNER JOIN FAMILY_ROOM_MAPPINGS fp ON fp.FAMILY_ID = fm.FAMILY_ID
                INNER JOIN HOME_ROOMS hr ON hr.ROOM_ID = fp.ROOM_ID
                WHERE fp.END_DATE IS NULL AND hr.OWNER_GROUP_ID = ".$id."
              ) as _head
            ) as _ages_stat";
    return $this->db->query($sql)->row_array();    
  }
  public function getPopCareerByOwnerId($id){
    $sql ="SELECT CAREER as career ,SUM(_value) as _value  FROM(
            SELECT p.CAREER ,COUNT(p.CAREER) as _value
            FROM PERSON_CURRENTS p
            INNER JOIN FAMILY_MEMBERS fm ON fm.PERS_ID = p.PERS_ID
            INNER JOIN FAMILIES f ON f.FAMILY_ID = fm.FAMILY_ID
            INNER JOIN FAMILY_ROOM_MAPPINGS fp ON fp.FAMILY_ID = fm.FAMILY_ID
            INNER JOIN HOME_ROOMS hr ON hr.ROOM_ID = fp.ROOM_ID
            WHERE fm.IS_STAY = TRUE AND hr.OWNER_GROUP_ID = ".$id."
            GROUP BY p.CAREER
          
          UNION
          
            SELECT p.CAREER ,COUNT(p.CAREER) as _value
            FROM PERSON_CURRENTS p
            INNER JOIN FAMILIES fm ON fm.PERS_ID = p.PERS_ID 
            INNER JOIN FAMILY_ROOM_MAPPINGS fp ON fp.FAMILY_ID = fm.FAMILY_ID
            INNER JOIN HOME_ROOMS hr ON hr.ROOM_ID = fp.ROOM_ID
            WHERE fp.END_DATE IS NULL AND hr.OWNER_GROUP_ID = ".$id."
          
        ) as _ages_stat
        WHERE CAREER IS NOT NULL
        GROUP BY CAREER
        ORDER BY _value desc";
    return $this->db->query($sql)->result_array();    
  }
  
}?>


