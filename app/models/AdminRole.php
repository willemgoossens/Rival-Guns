<?php
    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;
    
    class AdminRole extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('adminroles');
            $this->adminRightModel = $this->model('AdminRight');
        }


        /**
         * 
         * 
         * updateRightsForRole
         * @param Int roleId
         * @param Array Rights
         * @param Bool
         * 
         * 
         */
        public function updateRightsForRole (Int $roleId, Array $addedRights): Bool
        {
            if( $this->adminRightModel->deleteAdminRightsForRole($roleId) )
            {
                if( $this->adminRightModel->createAdminRightsForRole($roleId, $addedRights) )
                {
                    return true;
                }
            }

            return false;
        }


        /**
         * 
         * 
         * getRightNamesForRole
         * @param Int roleID
         * @return Array
         * 
         * 
         */
        public function getRightNamesForRole(?Int $roleId): Array
        {
            if( ! isset($roleId) )
            {
                return [];
            }

            $rights = $this->getRightsForRole($roleId);
            if( empty($rights) )
            {
                return [];
            }

            return array_column($rights, 'name');
        }



        /**
         * 
         * 
         * Get all the Rights for a specific role
         * @param Int roleId
         * @return Array
         * 
         */
        public function getRightsForRole(?Int $roleId): Array
        {
            if( ! isset($roleId) )
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
