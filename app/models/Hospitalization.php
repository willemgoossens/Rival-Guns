<?php
    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;
    
    class Hospitalization extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('hospitalizations');
            $this->dateTimeColumns = ['hospitalizedUntil', 'createdAt'];
        }


        /**
         * 
         * 
         * getDueHospitalizationForUser
         * @param Int userId
         * @param DateTime dateTime
         * @return NULL|Object
         * 
         * 
         */
        public function getDueHospitalizationForUserAndTime( Int $userId, DateTime $dateTime): ?Object
        {
            $this->db->query( "SELECT * 
                                FROM " . $this->getTableName() . "
                                WHERE userId = :userId
                                AND hospitalizedUntil <= :hospitalizedUntil" );
            $this->db->param( ":userId", $userId );
            $this->db->param( ":hospitalizedUntil", $dateTime->format( 'Y-m-d H:i:s' ) );
            $hospitalization = $this->db->single();

            if( $hospitalization )
            {
                return $hospitalization;
            }

            return NULL;
        }
        
    }