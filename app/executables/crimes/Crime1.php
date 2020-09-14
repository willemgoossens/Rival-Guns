<?php
    require_once dirname(__FILE__, 2) . './CrimeExecutable.php';

    class Crime1 extends CrimeExecutable
    {

        /**
         * 
         * 
         * Construct
         * @param Object User
         * 
         * 
         */
        public function __construct(object $user)
        {
            $this->user = $user;
        }


        /**
         * 
         * 
         * Initialize
         * @return Void
         * 
         * 
         */
        public function init(): Void
        {
            $this->addSummary( "You get in front of the supermarket.", "info" );

            if( $this->checkArrested() )
            {
                return;
            }

            $rewardAverage = 15;
            $rewardDeviation = 7;

            $rewardAverage = round( $rewardAverage * (1 + $this->user->bonusesIncluded->charismaSkills / 1000) );
            if( $rewardAverage > 70 )
            {
                $rewardAverage = 70;
            }

            $rewardDeviation = round( $rewardDeviation * (1 + $this->user->bonusesIncluded->charismaSkills / 1200));
            if( $rewardDeviation > 17 )
            {
                $rewardDeviation = 17;
            }

            $moneyReward = rand( $rewardAverage - $rewardDeviation , $rewardAverage + $rewardDeviation );
            $charismaReward = ceil( 5 - ( $this->user->charismaSkills / 1000 ) );

            $random = rand( 1 , 10 );
            if( $random >= 7 )
            {
                $this->scaredAway( $moneyReward, $charismaReward );
            }
            else
            {
                $this->addSummary( "You successfully make some good <strong>$" . $moneyReward ."</strong>", "success" );
                $this->addUserReward( "cash", $moneyReward );
                $this->addUserReward( "charismaSkills", $charismaReward );
                $this->setEnding( 1 );
            }

            $energy = - rand( 10, 20 ) / 10;
            $this->addUserReward( "energy", $energy );
        }



        /**
         * 
         * 
         * the user gets scared away
         * @param Int $moneyReward
         * @param Int $charismaReward
         * @param return Void
         * 
         * 
         */
        private function scaredAway(Int $moneyReward, Int $charismaReward): Void
        {
            $this->addSummary( "Halfway your run, the manager tells you to \"F*ck Off \".", "warning" );

            $moneyReward = round( $moneyReward / 2 );
            $charismaReward = ceil( $charismaReward / 2 );
            $this->addUserReward( "cash", $moneyReward );
            $this->addUserReward( "charismaSkills", $charismaReward);

            $randomNr = rand( 1, 2 );

            if(
                $this->user->bonusesIncluded->boxingSkills >= 100
                && $randomNr == 2
            ) {
                $this->addSummary( "You decide to fight the manager.", "info" );
                $this->addCrimeRecord( "minor assault" );

                $managerSkills = rand(80, 300);
                if( $this->user->bonusesIncluded->boxingSkills > $managerSkills )
                {
                    $this->addSummary( "You smash the fucker in the face and run.", "success" );

                    $addedBoxingSkills = ceil( 5 - $this->user->boxingSkills / 100 );          
                    $this->addUserReward( "boxingSkills", $addedBoxingSkills );
                    $this->setEnding( 2 );
                }
                else
                {
                    $this->addSummary( "You push the manager and he punches you in the face.", "danger" );

                    $health = -1 * rand( 50, 250 ) / 100;
                    $this->addUserReward( "health", $health );

                    $this->addSummary( "You decide to run off.", "info" );
                    $this->setEnding( 3 );
                }
            }
            else
            {
                $this->addSummary( "You decide to leave.", "info" );
                $this->setEnding( 4 );
            }
        }
    }
