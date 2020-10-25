<?php
    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;
    
    class CriminalRecord extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('criminalrecords');
        }

        
        /**
         * 
         * 
         * This function deletes all overdue crime records
         * @param Int userId
         * @return Bool
         * 
         * 
         */
        public function deleteOldRecords(Int $userId): Bool
        {
            $this->db->query("DELETE r.* 
                              FROM criminalrecords r
                              INNER JOIN crimetypes t ON r.type = t.id
                              WHERE r.createdAt < DATE_SUB(NOW(), INTERVAL t.expiredByTime SECOND)
                                AND userId = :userId
                                AND sentenceId IS NULL");
            
            $this->db->bind(':userId', $userId);
            
            return $this->db->execute();
        }


        /**
         * 
         * 
         * This function selects records for your arrest
         * @param Int userId
         * @return Array selectedRecords
         * 
         * 
         */
        public function selectRecordsForWhichArrested (Int $userId): Array
        {
            // Select last record(s) by default -> use a subquery
            // Select a random selection of other records.
            $this->db->query("SELECT *
                              FROM 
                                criminalrecords
                              WHERE 
                                userId = :userId
                                AND createdAt = (SELECT createdAt
                                                    FROM criminalrecords
                                                    WHERE userId = :userId
                                                    ORDER BY createdAt DESC
                                                    LIMIT 1)
                                AND sentenceId IS NULL");
            $this->db->bind(':userId', $userId);

            $arrestedForRecords = $this->db->resultSet();
            $arrestedForRecordsCount = count($arrestedForRecords);

            $totalAmountOfRecords = $this->countByUserId($userId);

            $randomSelectionOfOthersForWhichConvicted = [];
            if( $arrestedForRecordsCount < $totalAmountOfRecords )
            {
                $arrestedForRecordsIds = array_column($arrestedForRecords, 'id');

                $maxLimit = $totalAmountOfRecords - $arrestedForRecordsCount;
                $minLimit = ceil(($totalAmountOfRecords - $arrestedForRecordsCount) / 2);
                $limit = rand($minLimit, $maxLimit);

                $randomSelectionOfOthersForWhichConvicted = $this->orderBy("RAND()")
                                                                 ->limit($limit)
                                                                 ->getByUserIdAndNotIdAndSentenceId( $userId, $arrestedForRecordsIds, NULL );
            }

            $return = array_merge($arrestedForRecords, $randomSelectionOfOthersForWhichConvicted);
            return $return;
        }



        /**
         * 
         * 
         * This function calculates the wanted level of a user
         * @param Int userId
         * @return Float stars
         * 
         * 
         */
        public function calculateStars(Int $userId): Float
        {
            // Delete Old Records
            $this->deleteOldRecords($userId);

            $crimeTypes = $this->crimeTypeModel->orderBy('addStarsUntil')->get();

            $stars = 0;
            $overflow = 0;

            foreach( $crimeTypes as $type )
            {
                $records = $this->countByType($type->id);
                $starsAddition = $records * $type->addStars;
                
                if( $stars < $type->addStarsUntil )
                {
                    $stars += $starsAddition;

                    if( $stars > $type->addStarsUntil )
                    {
                        $overflow += $stars - $type->addStarsUntil;
                        $stars = $type->addStarsUntil;
                    }
                }
            }

            // The overflow is used to add a little bit more stars when a person commits a lot of crimes
            // However, it's divided by 10 to reduce it's importance
            $stars += ceil($overflow / 10 * 100) / 100;

            if( $stars > GAME_MAX_STARS )
            {
                $stars = GAME_MAX_STARS;
            }

            return $stars;
        }
    }