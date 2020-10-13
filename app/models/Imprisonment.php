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


        /**
         * 
         * 
         * insert
         * @param Array data
         * @param Bool unique
         * @param return mixed
         * 
         * 
         */
        public function insert( Array $values, Bool $returnId = false)
        {
            $existingImprisonment = $this->getSingleByUserId( $values['userId'] );

            if( $existingImprisonment )
            {
                $existingImprisonment->imprisonedUntil = new \DateTime( $existingImprisonment->imprisonedUntil );
                $values['imprisonedUntil'] = new \DateTime( $values['imprisonedUntil'] );
                $now = new \DateTime;

                $imprisonmentDuration = $now->diff( $values['imprisonedUntil'] );
                $existingImprisonment->imprisonedUntil->add( $imprisonmentDuration );

                $updateArray = [
                    'imprisonedUntil' => $existingImprisonment->imprisonedUntil->format( 'Y-m-d H:i:s' )
                ];
                $this->updateById( $existingImprisonment->id, $updateArray );

                return $existingImprisonment->id;
            }
            else
            {
                return parent::insert( $values, $returnId);
            }
        }
    }