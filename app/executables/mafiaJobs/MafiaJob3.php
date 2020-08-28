<?php
  require_once dirname(__FILE__, 2) . './CrimeExecutable.php';

  class MafiaJob3 extends CrimeExecutable
  {

      /**
       * construct
       * @param object $user The user object INCLUDING his stars level
       */
      public function __construct(object $user)
      {
        $this->user = $user;
      }



      /**
       * Initialize the story
       */
      public function init()
      {
        $energy = - rand(5, 20)/10;
        $this->addUserReward("energy", $energy);
        
        $this->addSummary("You enter the nightstore.", "info");

        if( $this->checkArrested() )
        {
            return;
        }

        $this->addSummary("You make the nightstore clerk an offer he can't refuse.", "info");

        $calculate = $this->user->bonusesIncluded->strengthSkills * 0.5 + $this->user->bonusesIncluded->charismaSkills;

        if( $calculate > 400 )
        {
            $calculate = 400;
        }

        $luck = rand(1 , 500);
        if( $luck >= $calculate )
        {
            $this->scaredAway();
        }
        else
        {
            $charismaReward = 0;
            if( $this->user->charismaSkills < 100 )
            {
                $charismaReward = rand(1, 4);
            }

            $this->addSummary("The clerck agrees to pay protection money.", "success");
            $this->addUserReward("charismaSkills", $charismaReward);

            $this->success();
            $this->setEnding(1);
        }
      }



        /**
         * the user gets scared away
         */
        private function scaredAway()
        {
            $this->addSummary("The clerk tells you to \"F*ck Off \".", "warning");

            $randomNr = rand(1, 3);

            if( $this->user->bonusesIncluded->boxingSkills > 100 
                || $randomNr == 3)
            {
                $this->addSummary("You grab the hard liquor display and throw it on the ground.", "info");

                $clerkBoxingSkills = rand(50, 300);
                $luck = rand(1, 5);

                if( $this->user->bonusesIncluded->boxingSkills > $clerkBoxingSkills
                    && $luck != 7)
                {
                    $this->addSummary("The nightstore clerk tries to push you out.", "info");
                    $this->addSummary("You smash the fucker in the face and he makes the wise decision to pay protection money.", "success");

                    $addedBoxingSkills = ceil(5 - $this->user->boxingSkills / 200);          
                    $this->addUserReward("boxingSkills", $addedBoxingSkills);

                    $this->success();
                    $this->setEnding(2);
                }
                else 
                {
                    $this->addSummary("The clerk runs up to you and hits you in the face with a baseball bat.", "danger"); 

                    $health = -1 * rand(250, 500) / 100;
                    $this->addUserReward("health", $health);

                    $this->addCrimeRecord("property destruction");
                    $this->addCrimeRecord("participation in organized crime");


                    $this->addSummary("You decide to run off.", "info");
                    $this->setEnding(3);
                }
            }
            else
            {
                $this->addSummary("The clerk grabs his phone and calls the police.", "danger"); 
                $this->addSummary("You decide to run off.", "info");

                $this->addCrimeRecord("participation in organized crime");
                $this->setEnding(4);
            }
        }

        /**
         * The user succeeds
         */
        private function success()
        {
            $moneyReward = 35;

            $this->addSummary("The mafia pays you <strong>$" . $moneyReward ."</strong> for your efforts.", "success");

            $this->addUserReward("cash", $moneyReward);
        }
  }
