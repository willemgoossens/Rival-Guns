<?php
    namespace App\Executables;

    abstract class CrimeExecutable
    {
        private $summary = [];
        private $userRewards = [];
        private $crimeRecords = [];
        private $items = [];
        private $arrested = false;
        private $testPHPUnitEnding;

        protected $user;


        /**
         * 
         * 
         * setEnding
         * @param Int EndingNr
         * @return Void
         * 
         * 
         */
        protected function setEnding (Int $endingNr): Void
        {
            $this->testPHPUnitEnding = $endingNr;
        }


        /**
         * 
         * 
         * addUserReward
         * @param String key
         * @param Int value
         * @return Void
         * 
         * 
         */
        protected function addUserReward (String $key, Int $value): Void
        {
            if( isset($this->userRewards[$key]) )
            {
                $this->userRewards[$key] += $value;
            }
            else
            {
                $this->userRewards[$key] = $value;
            }
        }


        /**
         * 
         * 
         * addSummary
         * @param String story
         * @param String class
         * @return Void
         * 
         * 
         */
        protected function addSummary (String $story, String $class = "info"): Void
        {
            $array = [
                "story" => $story,
                "class" => $class
            ];

            array_push($this->summary, $array);
        }


        /**
         * 
         * 
         * addCrimeRecord
         * @param String Crime
         * @return Void
         * 
         * 
         */
        protected function addCrimeRecord (String $crime): Void
        {
            $key = count($this->crimeRecords);
            $this->crimeRecords[$key] = $crime;
        }


        /** 
         * 
         * 
         * arrest the user
         * @return Void
         * 
         * 
         */
        public function arrest (): Void
        {
            $this->arrested = true;
            $this->items = [];
        }
        

        /**
         * 
         * 
         * this function checks whether the user should be arrested.
         * @param String Story
         * @return Bool
         * 
         * 
         */
        public function checkArrested (String $story = "A police officer recognizes you and peppersprays you... <br/><strong>You've got time!</strong>"): Bool
        {
            if(
                ! isset($this->user->stars)
                || $this->user->stars < 1
            ) {
                return false;
            }

            $luckUpperLimit = (GAME_MAX_STARS + 1) * 100;
            $luck = rand(0, $luckUpperLimit);
            $stars = $this->user->stars * 100;

            if( $stars < $luck )
            {
                return false;
            }

            $this->addSummary($story, "danger");
            $this->arrest();

            return true;
        }



        /**
         * 
         * 
         * return Summary
         * @return Array
         * 
         * 
         */
        public function returnSummary (): array
        {            
            $return = [
                "userRewards" => $this->userRewards,
                "crimeRecords" => $this->crimeRecords,
                "arrested" => $this->arrested,
                "storyline" => $this->summary,
                "items" => $this->items,
                "testPHPUnitEnding" => $this->testPHPUnitEnding
            ];

            return $return;
        }
    }