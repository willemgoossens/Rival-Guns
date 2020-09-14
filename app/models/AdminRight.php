<?php
    class AdminRight extends Model
    {

        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('adminrights');
        }

        /**
         * 
         * 
         * delete the Rights for a specific role
         * @param Int adminRoleId
         * @return Bool
         * 
         * 
         */
        public function deleteAdminRightsForRole(Int $adminRoleId): Bool
        {
            $this->db->query("DELETE
                                FROM adminroles_adminrights
                                WHERE adminroleid = :adminRoleId");
            $this->db->bind(':adminRoleId', $adminRoleId);

            if( $this->db->execute() )
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        
        /**
         * CreateAdminRightsForRole
         * @param Int roleId
         * @param Array rights
         * @return Mixed
         */
        public function createAdminRightsForRole(Int $adminRoleId, Array $adminRightIds)
        {
            $return = true;
            
            foreach( $adminRightIds as $adminRightId )
            {
                $this->db->query("INSERT INTO adminroles_adminrights
                                                    (adminroleid, adminrightid)
                                                    VALUES(:adminRoleId, :adminRightId)");

                $this->db->bind(':adminRoleId', $adminRoleId);
                $this->db->bind(':adminRightId', $adminRightId);

                if( ! $this->db->execute() )
                {
                    $return = false;
                }
            }

            return $return;
        }
    }
