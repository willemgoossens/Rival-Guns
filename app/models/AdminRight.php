<?php
  class AdminRight extends Model {

    public function __construct(){
      $this->db = new Database;
      $this->setTableName('adminrights');
    }

    /******************************
    *
    *
    * Delete admin rights for a specific role
    *
    *
    *******************************/
    public function deleteAdminRightsForRole($adminRoleId)
    {
      $this->db->query("DELETE
                        FROM adminroles_adminrights
                        WHERE adminroleid = :adminRoleId");
      $this->db->bind(':adminRoleId', $adminRoleId);

      if($this->db->execute())
      {
        return true;
      }else{
        return false;
      }
    }

    /******************************
    *
    *
    * Create admin rights for a specific role
    *
    *
    *******************************/
    public function createAdminRightsForRole($adminRoleId, $adminRightIds)
    {
      $return = true;
      
      foreach($adminRightIds as $adminRightId)
      {
        $this->db->query("INSERT INTO adminroles_adminrights
                          (adminroleid, adminrightid)
                          VALUES(:adminRoleId, :adminRightId)");

        $this->db->bind(':adminRoleId', $adminRoleId);
        $this->db->bind(':adminRightId', $adminRightId);

        if(!$this->db->execute())
        {
          $return = false;
        }
      }

      return $return;
    }
  }
