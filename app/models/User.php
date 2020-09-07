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

            if(!is_array($return))
            {
                $return = $this->countBonuses($return);
            }
            else 
            {
                foreach($return as &$object)
                {
                    $object = $this->countBonuses($object);
                }
    
            }

            return $return;
        }



        /**
         * modify Variables
         * @param object-array $user
         * @return object $user 
         */
        public function countBonuses($object)
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
            if(! empty($overlappingKeys))
            {
                $wearables = $this->wearableModel->getArrayByUserIdAndEquipped($object->id, true, "wearableCategoryId");
                $object->bonusesIncluded = new StdClass;
            }

            foreach($object as $key => &$value)
            {
                if($key == 'name')
                {
                    $value = ucfirst($value);
                }
                
                if(in_array($key, $skillNames))
                {
                    $skillName = $key;
                    
                    if(! isset($object->bonusesIncluded->$skillName ))
                    {
                        $object->bonusesIncluded->$skillName = $object->$skillName;
                    }

                    if( !empty($wearables))
                    {
                        $sqlQueryName = "getArrayByIdAndNot" . ucfirst($skillName) . "Bonus";
                        $bonuses = $this->wearableCategoryModel->$sqlQueryName($wearables, 1.00, $skillName . "Bonus");
                        
                        if(! empty($bonuses))
                        {
                            foreach($bonuses as $bonus)
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
        * login
        * @param: email
        * @param: password
        * @return: get users
        *
        ***/
        public function login(string $email, string $password)
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
        * is Logged in
        * @return: bool
        *
        ***/
        public function isLoggedIn()
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
         * arrest the user
         * @param int userId
         * 
         * 
         */
        public function arrest(int $userId): array
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
            if( !empty($wearables) )
            {
                foreach($wearables as $wearable)
                {
                    $isIllegal = $this->wearableCategoryModel->existsByIdAndIllegal($wearable->wearableCategoryId, true);

                    if($isIllegal)
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

            foreach($records as $record)
            {
                $this->criminalRecordModel->updateById($record->id, ['imprisonmentId' => $imprisonmentId]);
                
                if(isset($crimesNames[$crimeTypes[$record->type]->name]))
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
         * Hospitalize
         * @param int userId
         * @param string reason
         */
        public function hospitalize(int $userId, int $duration, string $reason): void
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
    }
