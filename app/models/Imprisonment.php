<?php
    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;
    
    class Imprisonment extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('imprisonments');
        }


        /**
         * 
         * 
         * finishDueImprisonmentForUserAndTime
         * @param Int userId
         * @param DateTime dateTime
         * @return Void
         * 
         * 
         */
        public function finishDueImprisonmentForUserAndTime( Int $userId, \DateTime $dateTime): Void
        {
            $this->db->query( "SELECT id 
                                FROM " . $this->getTableName() . "
                                WHERE userId = :userId
                                AND imprisonedUntil <= :imprisonedUntil" );
            $this->db->bind( ":userId", $userId );
            $this->db->bind( ":imprisonedUntil", $dateTime->format( 'Y-m-d H:i:s' ) );
            $imprisonment = $this->db->single();
            
            if( $imprisonment )
            {
                $this->deleteById( $imprisonment->id );
            }
        }
    }