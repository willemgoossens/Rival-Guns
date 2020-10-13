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
        }


        /**
         * 
         * 
         * getLaunderingLogOfTodayForUser
         * @param Int userId
         * @return Object log
         * 
         * 
         */
        public function getLaunderingLogOfTodayForUser( Int $userId ): Object
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
    }