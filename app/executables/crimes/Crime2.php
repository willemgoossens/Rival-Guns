<?php
  require_once APPROOT . '/executables/crimes/CrimeExecutable.php';

  class Crime2 extends CrimeExecutable
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
        $this->addSummary("You get in front of the jewelry store.", "info");

        if($this->checkArrested())
        {
          return;
        }

        $rewardAverage = 30;
        $rewardDeviation = 7;

        $rewardAverage = round($rewardAverage * (1 + $this->user->charisma / 1000));
        if($rewardAverage > 100)
        {
          $rewardAverage = 100;
        }

        $rewardDeviation = round($rewardDeviation * (1 + $this->user->charisma / 1200));
        if($rewardDeviation > 17)
        {
          $rewardDeviation = 17;
        }

        $moneyReward = rand($rewardAverage - $rewardDeviation , $rewardAverage + $rewardDeviation);
        $charismaReward = ceil(5 - ( $this->user->charisma / 1000 ));

        $random = rand(1 , 10);
        if($random >= 3)
        {
          $this->scaredAway($moneyReward);
        }
        else
        {
          $this->addSummary("You earn <strong>$" . $moneyReward ."</strong> from those filthy, rich bastards.", "success");
          $this->addUserReward("cash", $moneyReward);
          if($charismaReward > 0)
          {
            $this->addUserReward("charisma", $charismaReward);
          }  
        }

        $energy = - rand(10, 20)/10;
        $this->addUserReward("energy", $energy);
      }



      /**
       * the user gets scared away
       * @param int $moneyReward
       * @param int $charismaReward
       */
      private function scaredAway(int $moneyReward)
      {
        $guardAmount = rand(1,3);
        if($guardAmount == 1)
        {
            $this->addSummary("A security guard comes outside and tells you to leave.", "warning");
        }
        else
        {
            $this->addSummary($guardAmount . " security guards come outside and tell you to leave.", "warning");
        }

        $randomNr = rand(1, 2);

        if($this->user->boxing >= 300
           && $randomNr == 2)
        {
            if($guardAmount == 1)
            {
                $this->addSummary("You decide to fight him.", "info");
            }
            else
            {
                $this->addSummary("You decide to fight them.", "info");
            }

            for($i = 1; $i <= $guardAmount; $i++)
            {
                $this->addCrimeRecord("minor assault");
            }

            $securitySkills = rand(200, 400) * $guardAmount ^ 1.5;
            $badluckfactor = rand(1, 2);
            $difference = $securitySkills - $this->user->boxing;

            if($difference < 0)
            {
                if($guardAmount == 1)
                {
                    $this->addSummary("You smash the fucker in the face and run.", "success");
                }
                else
                {
                    $this->addSummary("After short fight, you kick their asses and run.", "success");
                }
                $this->addUserReward("cash", $moneyReward);

                $boxing = ceil(10 - $this->user->boxing / 100);   
                if($boxing > 0)
                {
                    $this->addUserReward("boxing", $boxing);
                }  
            }
            elseif($difference > 100 || $badluckfactor == 1)
            {
                if($guardAmount == 1)
                {
                    $this->addSummary("He punches you in the face, and calls the cops.", "danger");
                }
                else
                {
                    $this->addSummary("After kicking one of them in the crotch, they wrestle you to the ground and call the cops.", "danger");
                }

                $health = -1 * rand(50, 300) / 100;
                $this->addUserReward("health", $health);
                $this->arrest();

                $this->addSummary("You decide to run off.", "info");
            }
            else
            {
                $this->addSummary("After a short beating, you manage to escape. Lucky Bastard!", "warning");
                $this->addUserReward("cash", $moneyReward);

                $health = -1 * rand(50, 200) / 100;
                $this->addUserReward("health", $health);
                $boxing = ceil(8 - $this->user->boxing / 100);
                if($boxing > 0)
                {
                    $this->addUserReward("boxing", $boxing);
                }  
            }
        }
        else
        {
          $this->addSummary("You decide to leave.", "info");
        }
      }
  }
