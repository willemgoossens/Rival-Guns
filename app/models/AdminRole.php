<?php
  class AdminRole extends Model
  {

    public function __construct()
    {
      $this->db = new Database;
      $this->setTableName('adminroles');

      $children = [
        "adminRights" => [
                          "foreignTable" => "adminrights",
                          "foreignKey" => "id",
                          "currentKey" => "id",
                          "connectionTable" => "adminroles_adminrights",
                          "model" => "AdminRight"
                        ]
      ];

      $this->setChildren($children);
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
      if($this->child("adminRights")->deleteAdminRightsForRole($roleId))
      {
        if($this->child("adminRights")->createAdminRightsForRole($roleId, $addedRights))
        {
          return true;
        }
      }

      return false;
    }

    public function getRightsForInterface(?int $roleId)
    {
      if(! isset($roleId))
      {
        return [];
      }

      $rightIds = $this->getManyToManyIds("adminRights", $roleId);

      $rights = $this->child("adminRights")->getArrayById($rightIds, 'name');

      return $rights;
    }
  }
