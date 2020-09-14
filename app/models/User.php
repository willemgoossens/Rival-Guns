<?php
    class User extends Model 
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('users');

            $this->criminalRecordModel = $this->model('CriminalRecord');
            $this->crimeTypeModel = $this->model('CrimeType');
            $this->wearableCategoryModel = $this->model('WearableCategory');
            $this->wearableModel = $this->model('Wearable');
            $this->hospitalizationModel = $this->model('Hospitalization');
            $this->imprisonmentModel = $this->model('Imprisonment');
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
                $object->bonusesIncluded = new StdClass;
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
         * @return Array
         * 
         * 
         */
        public function arrest(Int $userId): Array
        {
            $records = $this->criminalRecordModel->selectArrestRecords($userId);


            $imprisonmentInsertArray = [
                'userId' => $userId,
                'department' => 'minimum'
            ];
            $imprisonmentId = $this->imprisonmentModel->insert($imprisonmentInsertArray, true);
            

            $updateArray = [
                'workingUntil' => null
            ];
            $this->updateById($userId, $updateArray);


            $wearables = $this->wearableModel->getByUserIdAndEquipped($userId, 1);
            if( ! empty($wearables) )
            {
                foreach( $wearables as $wearable )
                {
                    $isIllegal = $this->wearableCategoryModel->existsByIdAndIllegal($wearable->wearableCategoryId, true);

                    if( $isIllegal )
                    {
                        $this->wearableModel->deleteById($wearable->id);
                    }
                }
            }


            $crimeTypeIds = array_column($records, "type");
            $crimeTypes = $this->crimeTypeModel
                               ->getFlaggedUniqueById($crimeTypeIds); 
            $crimesNames = [];
            $totalJailTime = 0;

            foreach( $records as $record )
            {
                $this->criminalRecordModel->updateById($record->id, ['imprisonmentId' => $imprisonmentId]);
                
                if( isset($crimesNames[$crimeTypes[$record->type]->name]) )
                {
                    $crimesNames[$crimeTypes[$record->type]->name] += 1;
                }
                else
                {
                    $crimesNames[$crimeTypes[$record->type]->name] = 1;
                }

                $totalJailTime += $crimeTypes[$record->type]->jailTime;
            }

            $prisonReleaseDate = new DateTime("+" . $totalJailTime . " seconds");

            $returnArray = [
                'prisonReleaseDate' => $prisonReleaseDate->format('Y-m-d H:i:s'), 
                'arrestedFor' => $crimesNames
            ];

            return $returnArray;
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
        public function hospitalize(Int $userId, Int $duration, String $reason): Void
        {
            $insertArray = [
                'userId' => $userId,
                'duration' => $duration,
                'reason' => $reason
            ];

            $this->hospitalizationModel->insert($insertArray);

            $updateArray = [
                'workingUntil' => null
            ];

            $this->updateById($userId, $updateArray);
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
    }
