<?php
  require_once APPROOT . '/executables/CrimeExecutable.php';

  class Crime1 extends CrimeExecutable
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
        $this->addSummary("You get in front of the supermarket.", "info");

        if($this->checkArrested())
        {
          return;
        }

        $rewardAverage = 15;
        $rewardDeviation = 7;

        $rewardAverage = round($rewardAverage * (1 + $this->user->bonusesIncluded->charismaSkills / 1000));
        if($rewardAverage > 70)
        {
          $rewardAverage = 70;
        }

        $rewardDeviation = round($rewardDeviation * (1 + $this->user->bonusesIncluded->charismaSkills / 1200));
        if($rewardDeviation > 17)
        {
          $rewardDeviation = 17;
        }

        $moneyReward = rand($rewardAverage - $rewardDeviation , $rewardAverage + $rewardDeviation);
        $charismaReward = ceil( 5 - ( $this->user->charismaSkills / 1000 ) );

        $random = rand(1 , 10);
        if($random >= 7)
        {
          $this->scaredAway($moneyReward, $charismaReward);
        }
        else
        {
          $this->addSummary("You successfully make some good <strong>$" . $moneyReward ."</strong>", "success");
          $this->addUserReward("cash", $moneyReward);
          $this->addUserReward("charismaSkills", $charismaReward);
        }

        $energy = - rand(10, 20)/10;
        $this->addUserReward("energy", $energy);
      }



      /**
       * the user gets scared away
       * @param int $moneyReward
       * @param int $charismaReward
       */
      private function scaredAway(int $moneyReward, int $charismaReward)
      {
        $this->addSummary("Halfway your run, the manager tells you to \"F*ck Off \".", "warning");

        $moneyReward = round($moneyReward / 2);
        $charismaReward = ceil($charismaReward / 2);
        $this->addUserReward("cash", $moneyReward);
        $this->addUserReward("charismaSkills", $charismaReward);

        $randomNr = rand(1, 2);

        if($this->user->bonusesIncluded->boxingSkills >= 100
           && $randomNr == 2)
        {
          $this->addSummary("You decide to fight the manager.", "info");
          $this->addCrimeRecord("minor assault");

          $managerSkills = rand(80, 300);
          if($this->user->bonusesIncluded->boxingSkills > $managerSkills)
          {
            $this->addSummary("You smash the fucker in the face and run.", "success");

            $addedBoxingSkills = ceil(5 - $this->user->boxingSkills / 100);          
            $this->addUserReward("boxingSkills", $addedBoxingSkills);
          }
          else
          {
            $this->addSummary("You push the manager and he punches you in the face.", "danger");

            $health = -1 * rand(50, 250) / 100;
            $this->addUserReward("health", $health);

            $this->addSummary("You decide to run off.", "info");
          }
        }
        else
        {
          $this->addSummary("You decide to leave.", "info");
        }
      }
  }
