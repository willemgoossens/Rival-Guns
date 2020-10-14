<?php
    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;
    
    class CrimeType extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('crimetypes');
        }


        /**
         * 
         * 
         * getIdsForLaunderingCrimes
         * @return Array ids
         * 
         * 
         */
        public function getIdsForLaunderingCrimes(): Array
        {
            $this->db->query("SELECT id
                                FROM " . $this->getTableName() . "
                                WHERE name LIKE 'money laundering%'");
            $ids = $this->db->resultSetArray();

            return $ids;
        }
    }