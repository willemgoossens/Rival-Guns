<?php

    abstract class CrimeExecutable
    {
        private $summary = [];
        private $userRewards = [];
        private $crimeRecords = [];
        private $items = [];
        private $arrested = false;

        protected $user;

        protected function addUserReward(string $key, $value)
        {
            if(isset($this->userRewards[$key]))
            {
                $this->userRewards[$key] += $value;
            }
            else
            {
                $this->userRewards[$key] = $value;
            }
        }



        protected function addSummary(string $story, string $class)
        {
            $key = count($this->summary);
            $this->summary[$key]["story"] = $story;
            $this->summary[$key]["class"] = $class;
        }



        protected function addCrimeRecord(string $crime)
        {
            $key = count($this->crimeRecords);
            $this->crimeRecords[$key] = $crime;
        }



        /** 
         * arrest the user
         */
        public function arrest()
        {
            $this->arrested = true;
            $this->items = [];
        }

        

        /**
         * this function checks whether the user should be arrested.
         */
        public function checkArrested(string $story = "A police officer recognizes you and peppersprays you... <br/><strong>You've got time!</strong>")
        {
            if(! isset($this->user->stars)
                || $this->user->stars < 1)
            {
                return false;
            }

            $luckUpperLimit = (GAME_MAX_STARS + 1) * 100;
            $luck = rand(0, $luckUpperLimit);
            $stars = $this->user->stars * 100;

            if($stars > $luck)
            {
                return false;
            }

            $this->addSummary($story, "danger");
            $this->arrest();

            return true;
        }



        /**
         * return all data
         */
        public function returnSummary()
        {
            $return = [
                "userRewards" => $this->userRewards,
                "crimeRecords" => $this->crimeRecords,
                "arrested" => $this->arrested,
                "storyline" => $this->summary,
                "items" => $this->items
            ];

            return $return;
        }
    }