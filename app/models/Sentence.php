<?php

    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;

    class Sentence extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('sentences');
            $this->dateTimeColumns = ['createdAt'];
        }


        /**
         * 
         * 
         * createSentenceForUser
         * @param Int userId
         * @return Void
         * 
         * 
         */
        public function createSentenceForUser( Int $userId ): Void
        {
            $criminalRecords = $this->criminalRecordModel->selectRecordsForWhichArrested( $userId );

            $crimeTypeIds = array_column( $criminalRecords, "type" );
            $crimeTypes = $this->crimeTypeModel
                               ->getFlaggedUniqueById($crimeTypeIds);
            $totalJailTime = 0;

            foreach( $criminalRecords as $record )
            {
                $totalJailTime += $crimeTypes[$record->type]->jailTime;
            }

            $insertSentenceArray = [
                'userId' => $userId,
                'timeRemaining' => $totalJailTime
            ];
            $sentenceId = $this->insert( $insertSentenceArray, true );

            $updateRecordArray = [
                'sentenceId' => $sentenceId
            ];
            foreach( $criminalRecords as $record )
            {
                $this->criminalRecordModel->updateById( $record->id, $updateRecordArray );
            }
        }


        /**
         * 
         * 
         * getSentencesForUser
         * @param Int userId
         * @return Array
         * 
         * 
         */
        public function getSentencesForUser( Int $userId ): Array
        {
            $sentences = $this->sentenceModel->orderBy( 'createdAt', 'DESC' )
                                             ->getByUserId( $userId );

            foreach( $sentences as &$sentence )
            {
                $countedCriminalRecords = $this->criminalRecordModel->groupBy( 'type' )
                                                             ->getBySentenceId( $sentence->id, 'type', 'COUNT(*) AS amount' );
                $sentence->criminalRecords = [];
                foreach( $countedCriminalRecords as $record )
                {
                    $crimeType = $this->crimeTypeModel->getSingleById( $record->type, 'name' );
                    $sentence->criminalRecords[$crimeType->name] = $record->amount;
                }
            }

            return $sentences;
        }


        /**
         * 
         * 
         * finishDueSentencesForUser
         * @param Int userId
         * @param DateTime dateTime
         * @param Return Void
         * 
         */
        public function finishDueSentencesForUserAndTime( Int $userId, \DateTime $dateTime ): Void
        {
            $imprisonment = $this->imprisonmentModel->getSingleByUserId( $userId );

            if( ! $imprisonment )
            {
                return;
            }

            $sentences = $this->getByUserId( $userId );
            $totalTime = array_sum( array_column( $sentences, "timeRemaining") );
            $imprisonment->createdAt->modify('+' . $totalTime . ' second');
            
            if( $imprisonment->createdAt < new \DateTime )
            {
                $this->db->query("DELETE 
                                    FROM " . $this->getTableName() . "
                                    WHERE
                                        userId = :userId");
                $this->db->bind( ":userId", $userId );
                $this->db->execute();

                $this->imprisonmentModel->deleteById( $imprisonment->id );
            }
        }
    }