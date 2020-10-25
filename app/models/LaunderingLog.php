<?php

    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;

    class LaunderingLog extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('launderinglogs');
            $this->dateTimeColumns = ['createdAt'];
        }


        /**
         * 
         * 
         * getLaunderingLogOfTodayForUser
         * @param Int userId
         * @return Mixed log
         * 
         * 
         */
        public function getLaunderingLogOfTodayForUser( Int $userId )
        {
            $startOfToday = new \DateTime('today');
            $endOfToday = new \DateTime('tomorrow');

            $this->db->query('SELECT *
                                FROM ' . $this->getTableName() . '
                                WHERE userId = :userId 
                                AND createdAt >= :startOfToday
                                And createdAt < :endOfToday');
            $this->db->bind( ":userId", $userId);
            $this->db->bind( ":startOfToday", $startOfToday->format( 'Y-m-d H:i:s' ) );
            $this->db->bind( ":endOfToday", $endOfToday->format( 'Y-m-d H:i:s' ) );

            $log = $this->db->single();

            return $log;
        }


        /**
         * 
         * 
         * deleteAllLaunderingLogsForUser
         * @param Int userId
         * @return Void
         * 
         * 
         */
        public function deleteAllLaunderingLogsForUser( Int $userId ): Void
        {
            $this->db->query('DELETE
                                FROM ' . $this->getTableName() . '
                                WHERE userId = :userId');
            $this->db->bind( ':userId', $userId );
            $this->db->execute();
        }
    }