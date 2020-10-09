<?php

    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;

    class Job extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('jobs');
        }

        /**
         * 
         * 
         * createHooversJob
         * @param Int userId
         * @param Bool Success
         * 
         * 
         */
        public function createHooversJob( Int $userId ): Bool
        {
            $user = $this->userModel->getSingleById( $userId );

            $max = ceil( ( $user->charismaSkills + 1 ) / 100 );    

            if( $max > 5 )
            {
                $max = 5;
            }            

            $amountOfVacuums = rand( 0, $max );
            $bankReward = $amountOfVacuums * 40;
            $charismaSkillsReward = $amountOfVacuums;


            $notificationBody = "You have successfully sold " . $amountOfVacuums . " vacuum cleaners and made <strong>&euro;" . $bankReward . "</strong>";
            $notificationClass = "alert-success";

            if( $amountOfVacuums == 0 )
            {
                $notificationBody = "You have sold no vacuum cleaners at all...";
                $notificationClass = "alert-warning";
            }
            elseif( $amountOfVacuums == 1 )
            {
                $notificationBody = "You have successfully sold " . $amountOfVacuums . " vacuum cleaner and made <strong>&euro;" . $bankReward . "</strong>";
            }

            $futureTimestamp = new \DateTime();
            $futureTimestamp->modify( '+15 minutes' );

            $workingUntil = $futureTimestamp->format( 'Y-m-d H:i:s' );

            $insertJobArray = [
                'type' => 'Hoovers',
                'workingUntil' => $workingUntil,
                'userId' => $userId,
                'bankReward' => $bankReward,
                'charismaSkillsReward' => $charismaSkillsReward,
                'notificationText' => $notificationBody,
                'notificationClass' => $notificationClass,
                'notificationLink' => '/locations/hoovers'
            ];

            return $this->insert( $insertJobArray);
        }


        /**
         * 
         * 
         * finishDueJobForUserAndTime
         * @param Int userId
         * @param DateTime dateTime
         * @return Void
         * 
         * 
         */
        public function finishDueJobForUserAndTime( Int $userId, \DateTime $dateTime): Void
        {
            $this->db->query( "SELECT * 
                                FROM " . $this->getTableName() . "
                                WHERE userId = :userId
                                AND workingUntil <= :workingUntil" );
            $this->db->bind( ":userId", $userId );
            $this->db->bind( ":workingUntil", $dateTime->format( 'Y-m-d H:i:s' ) );
            $job = $this->db->single();
            echo ("<br/>test<br/>" . $dateTime->format( 'Y-m-d H:i:s' ) . "<br/>");
            if( ! $job )
            {
                return;
            }

            $user = $this->userModel->getSingleById( $userId, 'charismaSkills', 'bank', 'lastCheckedAt');

            // Create a new conversation
            $insertNotificationData = [
                'userId' => $job->userId,
                'class' => $job->notificationClass,
                'body' => $job->notificationText,
                'link' => $job->notificationLink,
                'createdAt' => $job->workingUntil
            ];

            $this->notificationModel->insert($insertNotificationData);

            $user->bank += $job->bankReward;
            $user->charismaSkills += $job->charismaSkillsReward;
            
            $updateArray = 
            [
                'bank' => $user->bank,
                'charismaSkills' => $user->charismaSkills
            ];

            $this->userModel->updateById($job->userId, $updateArray);

            $this->deleteById( $job->id );
        }


        /**
         * 
         * 
         * deleteJobsForUser
         * @param Int $userId
         * @return Void
         * 
         * 
         */
        public function deleteJobsForUser( Int $userId ): Void
        {

            $this->db->query("DELETE FROM " . $this->getTableName() . " WHERE userId = :userId");
            $this->db->bind(":userId", $userId);
            $this->db->execute();
        }
    }