<?php
    class User extends Model 
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('users');

            $children = [
              "criminalRecords" => [
                                "foreignTable" => "criminalrecords",
                                "foreignKey" => "userId",
                                "currentKey" => "id",
                                "model" => "CriminalRecord"
                              ]
            ];

            $this->setChildren($children);
        }

        /**
         * This magic function intercepts the __call function to the Model class
         */
        public function __call($method, $arguments)
        {
            $return = parent::__call($method, $arguments);

            if(is_array($return))
            {
                foreach($return as &$subObject)
                {
                    if(is_object($subObject))
                    {
                        if(isset($subObject->name))
                        {
                          $subObject->name = ucfirst($subObject->name);
                        }
                    }
                    elseif(is_array($subObject))
                    {
                        if(isset($subObject["name"]))
                        {
                            $subObject["name"] = ucfirst($subObject["name"]);
                        }
                    }
                }
            }
            elseif(is_object($return))
            {
                if(isset($return->name))
                {
                  $return->name = ucfirst($return->name);
                }
            }

            return $return;
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
         * arrest the user
         * @param int userId
         */
        public function arrest(int $userId): array
        {
            $records = $this->child("criminalRecords")->selectArrestRecords($userId);
            $crimeTypeIds = array_column($records, "type");

            $crimeTypes = $this->child("criminalRecords")
                               ->parent("crimeTypes")
                               ->getFlaggedUniqueById($crimeTypeIds);
            
            $totalJailTime = 0;
            $crimesNames = [];

            foreach($records as $record)
            {
                $totalJailTime += $crimeTypes[$record->type]->jailTime;
                $this->child("criminalRecords")->deleteById($record->id);
                
                if(isset($crimesNames[$crimeTypes[$record->type]->name]))
                {
                    $crimesNames[$crimeTypes[$record->type]->name] += 1;
                }
                else
                {
                    $crimesNames[$crimeTypes[$record->type]->name] = 1;
                }
            }

            $endJailDate = new DateTime("+" . $totalJailTime . " seconds");
            
            $updateArray = [
                'inJailUntil' => $endJailDate->format('Y-m-d H:i:s')
            ];

            $this->updateById($userId, $updateArray);

            return [$endJailDate->format('Y-m-d H:i:s'), $crimesNames];
        }
    }
