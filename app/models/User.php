<?php
    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;

    class User extends Model 
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('users');
            $this->dateTimeColumns = ['lastCheckedAt', 'createdAt'];
        }

        /**
         * This magic function intercepts the __call function to the Model class
         */
        public function __call($method, $arguments)
        {
            $return = parent::__call($method, $arguments);

            if( is_object($return) )
            {
                $return = $this->countBonuses($return);
            }
            elseif( is_array($return) )
            {
                foreach( $return as &$object )
                {
                    $object = $this->countBonuses($object);
                }
    
            }

            return $return;
        }



        /**
         * 
         * 
         * modify Variables
         * @param Object $user
         * @return Object $user 
         * 
         * 
         */
        public function countBonuses(Object $object): Object
        {
            $skillNames = [
                "agilitySkills",
                "boxingSkills",
                "burglarySkills",
                "carTheftSkills",
                "charismaSkills",
                "drivingSkills",
                "enduranceSkills",
                "pistolSkills",
                "rifleSkills",
                "robbingSkills",
                "stealingSkills",
                "strengthSkills"
            ];

            $keys = array_keys(get_object_vars($object));
            $overlappingKeys = array_intersect($keys, $skillNames);
            if(! empty($overlappingKeys) )
            {
                $wearables = $this->wearableModel->getArrayByUserIdAndEquipped($object->id, true, "wearableCategoryId");
                $object->bonusesIncluded = new \StdClass;
            }

            foreach( $object as $key => &$value )
            {
                if($key == 'name')
                {
                    $value = ucfirst($value);
                }
                
                if( in_array($key, $skillNames) )
                {
                    $skillName = $key;
                    
                    if(! isset($object->bonusesIncluded->$skillName) )
                    {
                        $object->bonusesIncluded->$skillName = $object->$skillName;
                    }

                    if( !empty($wearables) )
                    {
                        $sqlQueryName = "getArrayByIdAndNot" . ucfirst($skillName) . "Bonus";
                        $bonuses = $this->wearableCategoryModel->$sqlQueryName($wearables, 1.00, $skillName . "Bonus");
                        
                        if(! empty($bonuses) )
                        {
                            foreach( $bonuses as $bonus )
                            {
                                $object->bonusesIncluded->$skillName = round( $object->$skillName * $bonus * 10 ) / 10;
                            }
                        }
                    }
                }
            }
            return $object;
        }



        /**
        *
        *
        * Login
        * @param String email
        * @param String password
        * @return Mixed False or array
        *
        *
        */
        public function login(String $email, String $password)
        {
            $user = $this->getSingleByEmail($email);

            $hashed_password = $user->password;
            if(password_verify($password, $hashed_password))
            {
                return $user;
            } 
            else 
            {
                return false;
            }
        }



        /**
        *
        *
        * isLoggedIn
        * @return Bool
        *
        *
        */
        public function isLoggedIn(): Bool
        {
          // Check if session_id has been set
            if(isset($_SESSION['userId']))
            {
                // Get the amount
                // Now check if we need to update the apiKey
                $user = $this->getById($_SESSION['userId']);
                return true;
            } 
            else 
            {
                return false;
            }
        }



        /**
         *
         * 
         * Arrest
         * @param Int userId
         * @return Void
         * 
         * 
         */
        public function arrest(Int $userId): Void
        {
            $this->sentenceModel->createSentenceForUser( $userId ); 
            
            $this->imprisonmentModel->imprisonUser( $userId );

            $this->jobModel->deleteJobsForUser( $userId );

            $this->wearableModel->deleteIllegalEquippedWearablesForUser( $userId );
        }


        /**
         * 
         * 
         * Hospitalize
         * @param Int userId
         * @param Int $duration
         * @param String reason
         * @return Void
         * 
         * 
         */
        public function hospitalize( Int $userId, Int $duration, String $reason ): Void
        {
            $hospitalizedUntil = new \DateTime;
            $hospitalizedUntil->modify( '+' . $duration . ' seconds');

            $insertArray = [
                'userId' => $userId,
                'hospitalizedUntil' => $hospitalizedUntil->format( 'Y-m-d H:i:s' ),
                'reason' => $reason
            ];

            $this->hospitalizationModel->insert($insertArray);

            $job = $this->jobModel->deleteJobsForUser( $userId );
        }

        /**
         * 
         * 
         * createUserSession
         * @param Object User
         * @return Void
         * 
         * 
         */
        public function createUserSession (Object $user): Void
        {
            $_SESSION['userId'] = $user->id;
            redirect('');
        }


        /**
         * 
         * 
         * updateUserData
         * @param Int userId
         * @return Void
         * 
         * 
         */
        public function updateUserData( Int $userId ): Void
        {
            $eventTimestamps = $this->userModel->getEventTimestampsForUser( $userId );
            $eventTimestamps = new \ArrayIterator( $eventTimestamps );
            
            $now = new \DateTime;

            $user = $this->getSingleById( $userId );

            foreach($eventTimestamps as $eventTimestamp)
            {                
                if( $eventTimestamp > $now )
                {
                    break;
                }

                $this->calculateHealthAndEnergyForUser( $userId, $eventTimestamp );
                $this->propertyModel->calculateProfitsForUserAndTime( $userId, $eventTimestamp );

                $this->propertyModel->finishInstallationForUserAndTime( $userId, $eventTimestamp );                
                $this->jobModel->finishDueJobForUserAndTime( $userId, $eventTimestamp );                                
                
                $ranFutImpr = $this->futureImprisonmentModel->finishDueFutureImprisonmentsForUserAndTime( $userId, $eventTimestamp );
                // We need to double check in case the user was also arrested for illegal things
                if( $ranFutImpr )
                {
                    $imprisonmentTime = $this->imprisonmentModel->getEndOfImprisonmentForUser( $userId );
                    if( ! empty( $imprisonmentTime ) )
                    {
                        $eventTimestamps->append( $imprisonmentTime );
                        $eventTimestamps->asort();
                    }
                }

                $this->sentenceModel->finishDueSentencesForUserAndTime( $userId, $eventTimestamp );

                $user->lastCheckedAt = $eventTimestamp;

                $this->db->query("UPDATE users SET lastCheckedAt = :time
                                WHERE id = :userId");
                $this->db->bind(":time", $eventTimestamp->format( 'Y-m-d H:i:s' ));
                $this->db->bind(":userId", $userId);
                $this->db->execute();
            }
        }


        /**
         * 
         * 
         * getEventTimestampsForUser
         * @param Int userId
         * @return Array
         * 
         * 
         */
        public function getEventTimestampsForUser( Int $userId ): Array
        {
            $timestamps = [];

            $installationTimes = $this->propertyModel->getFlaggedUniqueByUserIdAndNotInstallingUntil( $userId, NULL, 'installingUntil' );
            if( ! empty($installationTimes) )
            {
                $installationTimes = array_map( function($val){ return strtotime($val->installingUntil); }, $installationTimes);
                array_push( $timestamps, ...$installationTimes );
            }
            
            $futureImprisonmentTimes = $this->futureImprisonmentModel->getFutureImprisonmentTimestampsForUser( $userId );
            if( ! empty( $futureImprisonmentTimes ) )
            {
                $timestamps = array_merge( $timestamps, $futureImprisonmentTimes );
            }
            
            $imprisonmentTime = $this->imprisonmentModel->getEndOfImprisonmentForUser( $userId );
            if( ! empty( $imprisonmentTime ) )
            {
                array_push( $timestamps, $imprisonmentTime );
            }

            $hospitalizationTime = $this->hospitalizationModel->getSingleByUserId( $userId, 'hospitalizedUntil' );
            if( ! empty( $hospitalizationTime ) )
            {
                array_push( $timestamps, $hospitalizationTime->hospitalizedUntil );
            }

            $jobTime = $this->jobModel->getSingleByUserId( $userId, 'workingUntil' );
            if( ! empty( $jobTime ) )
            {
                array_push( $timestamps, $jobTime->workingUntil );
            }

            $now = new \DateTime;
            array_push( $timestamps, $now );

            sort($timestamps);

            return $timestamps;
        }


        /**
         * 
         * 
         * calculateHealthAndEnergyForUser
         * @param Int userId
         * @param DateTime dateTime
         * @return NULL|Int Id
         * 
         * 
         */
        public function calculateHealthAndEnergyForUser( Int $userId, \DateTime $dateTime): Void
        {
            // This function needs to be checked
            $user = $this->getSingleById( $userId );
            $userLastCheckedDuplicate = $user->lastCheckedAt;

            $hospitalization = $this->hospitalizationModel->getSingleByUserId( $userId );

            if( $hospitalization )
            {
                if( $hospitalization->hospitalizedUntil > $dateTime )
                {
                    $usedTime = $dateTime;
                }
                else
                {
                    $usedTime = $hospitalization->hospitalizedUntil;
                }
                
                $difference = $usedTime->getTimestamp() - $userLastCheckedDuplicate->getTimestamp();

                $user->health += ($difference / 60) * GAME_HEALTH_INCREASE_PER_MINUTE_HOSPITAL;                    
                if( $user->health > 100 )
                {
                    $user->health = 100;
                }

                $user->energy += ($difference / 60) * GAME_ENERGY_INCREASE_PER_MINUTE_HOSPITAL;
                if( $user->energy > 100 )
                {
                    $user->energy = 100;
                }

                $userLastCheckedDuplicate = $usedTime;

                if( $hospitalization->hospitalizedUntil <= $dateTime)
                {
                    $this->hospitalizationModel->deleteById( $hospitalization->id );
                }
            }


            $difference = $dateTime->getTimestamp() - $userLastCheckedDuplicate->getTimestamp();

            $user->health += ($difference / 60) * GAME_HEALTH_INCREASE_PER_MINUTE;                    
            if( $user->health > 100 )
            {
                $user->health = 100;
            }

            $user->energy += ($difference / 60) * GAME_ENERGY_INCREASE_PER_MINUTE;
            if( $user->energy > 100 )
            {
                $user->energy = 100;
            }
            
            $updateArray = [
                "health" => $user->health, 
                "energy" => $user->energy, 
                "lastCheckedAt" => $user->lastCheckedAt->format('Y-m-d H:i:s')
            ];

            $this->userModel->updateById( $user->id, $updateArray );
        }


        /**
         * 
         * 
         * calculateReleaseDateForUser
         * @param Int userId
         * @return Null|Datetime
         * 
         * 
         */
        public function calculateReleaseDateForUser( Int $userId ): ?\DateTime
        {
            $sentences = $this->sentenceModel->getByUserId( $userId );

            if( empty( $sentences ) )
            {
                return null;
            }

            $totaltime = array_sum( array_column( $sentences, "timeRemaining") );

            $imprisonment = $this->imprisonmentModel->getSingleByUserId( $userId );
            $imprisonedUntil = $imprisonment->createdAt->modify('+' . $totaltime . ' second');

            return $imprisonedUntil;
        }
    }
