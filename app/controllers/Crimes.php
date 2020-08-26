<?php
    class Crimes extends Controller
    {

        public function __construct()
        {
            $this->userModel = $this->model('User');
            $this->crimeModel = $this->model('Crime');
            $this->crimeCategoryModel = $this->model('CrimeCategory');
            $this->crimeTypeModel = $this->model('CrimeType');
            $this->criminalRecordModel = $this->model('CriminalRecord');
            $this->adminRoleModel = $this->model('AdminRole');
            $this->conversationModel = $this->model('Conversation');

            // Set the sessions for the nav bar
            $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
            $this->data['user']->adminRights         = $this->adminRoleModel->getRightNamesForRole($this->data['user']->adminRole);
            $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
        }


        
        /**
         * index
         */
        public function index(int $categoryId)
        {
            $crimeCategory = $this->crimeCategoryModel->getSingleById($categoryId);
            if(!$crimeCategory)
            {
              redirect('posts');
            }

            $user = &$this->data['user'];
            if($user->health < 5)
            {
                $this->data['lowHealthWarning'] = true;
            }
            if($user->energy < 5)
            {
                $this->data['lowEnergyWarning'] = true;
            }


            $crimes = $this->crimeModel->getByCrimeCategoryId($categoryId);

            $this->data['crimes'] = $crimes;
            $this->data['title'] = $crimeCategory->name;

            $this->view('crimes/index', $this->data);
        }



        /**
         * commit a crime
         * @param  int    $crimeId [description]
         */
        public function commit(int $crimeId)
        {

            $user = &$this->data['user'];
            $user->stars = $this->criminalRecordModel->calculateStars($user->id);

            $crime = $this->crimeModel->getSingleById($crimeId);
            $category = $this->crimeCategoryModel->getSingleById($crime->crimeCategoryId);

            if($category->mainCategory == 'Crimes')
            {
                $path = "./executables/crimes/Crime" . $crime->id . ".php";
                $className = 'Crime' . $crime->id;
            }
            elseif($category->mainCategory == 'Mafia Jobs')
            {
                $path = "./executables/mafiaJobs/MafiaJob" . $crime->id . ".php";
                $className = 'MafiaJob' . $crime->id;
            }

            if( !$crime
                || $user->health < 5
                || $user->energy < 5
                || !file_exists(APPROOT . $path))
            {
                redirect('posts');
            }
            echo APPROOT . $path;
            require_once APPROOT . $path;
            $crime = new $className($user);
            $crime->init();

            $summary = $crime->returnSummary();



            $text = [];
            foreach($summary["storyline"] as $turn)
            {
                array_push($text, ["story" => $turn["story"], "class" => $turn["class"]]);
            }



            $updateArray = [];
            foreach($summary["userRewards"] as $name => $reward)
            {
                if(! isset($user->$name) )
                {
                    throw new Exception("You've tried to set an unexisting user variable?", 1);          
                }

                if( $reward == 0)
                {
                    unset($summary["userRewards"][$name]);
                    break;
                }

                $user->$name += $reward;
                $updateArray[$name] = $user->$name;

                if(substr($name, -6, 6) == "Skills")
                {
                    unset($summary["userRewards"][$name]);
                    $abbreviatedName = substr($name, 0, -6);
                    $summary["userRewards"][$abbreviatedName] = $reward;
                }
            }
            
            // Perform the database functions
            if(!empty($updateArray))
            {
                $this->userModel->updateById($user->id, $updateArray);
            }



            foreach($summary["crimeRecords"] as $record)
            {
                $crimeType = $this->crimeTypeModel->getSingleByName($record, 'id');
                if(! $crimeType)
                {
                    throw new Exception("Yeah, well, this crime doesn't exist.", 1);          
                }

                $insertArray = [
                    "userId" => $user->id,
                    "type" => $crimeType->id
                ];

                $this->criminalRecordModel->insert($insertArray);
            }




            if($summary["arrested"])
            {        
                list($user->inJailUntil, $this->data["arrestedFor"]) = $this->userModel->arrest($user->id);
            }


            
            $this->data["arrested"] = $summary["arrested"];
            $this->data["crimeRecords"] = $summary["crimeRecords"];
            $this->data["storyline"] = $text;
            
            $this->data["title"] = $category->name;
            $this->data["userRewards"] = $summary["userRewards"];

            $this->view('crimes/commit', $this->data);
        }
    }
