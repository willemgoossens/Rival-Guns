<?php
  class AdminRole extends Model
  {

    public function __construct()
    {
      $this->db = new Database;
      $this->setTableName('adminroles');
      $this->adminRightModel = $this->model('AdminRight');
    }

    /******************************
    *
    *
    * Update rights for role
    * @PARAM: int - roleId
    * @PARAM: array - added Rights
    *
    *
    *******************************/
    public function updateRightsForRole(int $roleId, array $addedRights)
    {
      if($this->adminRightModel->deleteAdminRightsForRole($roleId))
      {
        if($this->adminRightModel->createAdminRightsForRole($roleId, $addedRights))
        {
          return true;
        }
      }

      return false;
    }

    public function getRightNamesForRole(?int $roleId)
    {
      if(! isset($roleId))
      {
        return [];
      }

      $this->db->query("SELECT adminRightId
                    FROM adminroles_adminrights
                    WHERE adminRoleId = :adminRoleId");
      $this->db->bind(':adminRoleId', $roleId);

      $rightIds = $this->db->resultSetArray();

      $rights = $this->adminRightModel->getArrayById($rightIds, 'name');

      return $rights;
    }



    /**
     * Get all the Rights for a specific role
     * @param int roleId
     */
    public function getRightsForRole(?int $roleId)
    {
      if(! isset($roleId))
      {
        return [];
      }

      $this->db->query("SELECT adminRightId
                    FROM adminroles_adminrights
                    WHERE adminRoleId = :adminRoleId");
      $this->db->bind(':adminRoleId', $roleId);

      $rightIds = $this->db->resultSetArray();

      $rights = $this->adminRightModel->getById($rightIds);

      return $rights;
    }
  }
