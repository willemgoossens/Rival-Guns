<?php
    namespace App\Middleware;
    use App\Libraries\Middleware as Middleware;

    class HealthCheck extends Middleware
    {
        public function __construct(string ...$setup)
        {
        $this->setVariables(...$setup);
        }


        /**
         * 
         * 
         * Before
         * @return Bool
         * 
         * 
         */
        public function before(): Bool
        {
            $user = $this->userModel->getSingleById($_SESSION["userId"]);
            $hospitalization = $this->hospitalizationModel->getSingleByUserId($_SESSION["userId"]);
            
            $now = new \DateTime();

            if( $hospitalization )
            {
                $hos = $hospitalization;
                $hos->hospitalizedUntil = new \DateTime($hos->hospitalizedUntil);

                $user->lastCheckedAt = new \DateTime($user->lastCheckedAt);

                if( $hos->hospitalizedUntil < $now )
                {                
                    $difference = $hos->hospitalizedUntil->getTimestamp() - $user->lastCheckedAt->getTimestamp();
    
                    $user->health += ($difference / 60) * GAME_HEALTH_INCREASE_PER_MINUTE_HOSPITAL;                    
                    if( $user->health > 100 )
                    {
                        $user->health = 100;
                    }
    
                    $user->energy += ($difference / 60) * GAME_ENERGY_INCREASE_PER_MINUTE_HOSPITAL;
                    if( $user->energy > 100 )
                    {
                        $user->energy = 100;
                    }

                    $difference = $now->getTimestamp() - $hos->hospitalizedUntil->getTimestamp();
    
                    $user->health += ($difference / 60) * GAME_HEALTH_INCREASE_PER_MINUTE;
                    if( $user->health > 100 )
                    {
                        $user->health = 100;
                    }
    
                    $user->energy += ($difference / 60) * GAME_ENERGY_INCREASE_PER_MINUTE;
                    if( $user->energy > 100 )
                    {
                        $user->energy = 100;
                    }

                    $this->hospitalizationModel->deleteById($hos->id);
                }
                else
                {              
                    $difference = $now->getTimestamp() - $user->lastCheckedAt->getTimestamp();
    
                    $user->health += ($difference / 60) * GAME_HEALTH_INCREASE_PER_MINUTE_HOSPITAL;                    
                    if( $user->health > 100 )
                    {
                        $user->health = 100;
                    }
    
                    $user->energy += ($difference / 60) * GAME_ENERGY_INCREASE_PER_MINUTE_HOSPITAL;
                    if( $user->energy > 100 )
                    {
                        $user->energy = 100;
                    }

                    if(
                        $this->controller != "hospitalizations" 
                        || $this->method != "hospitalized"
                    ) {
                        redirect('hospitalized');
                    }
                }
                
                $this->userModel->updateById($user->id, ["health" => $user->health, "energy" => $user->energy, "lastCheckedAt" => $now->format('Y-m-d H:i:s')]);

                return false;
            }
            else
            {
                if( $user->health == 0 )
                {
                    $insertArray = [
                        'userId' => $user->id,
                        'duration' => 5 * 60,
                        'reason' => 'bled out'
                    ];

                    $this->hospitalizationModel->insert($insertArray);
                    redirect('hospitalized');
                }

                if( $this->controller == "hospitalizations" )
                {
                    redirect('');
                }

                $user->lastCheckedAt = strtotime($user->lastCheckedAt);
                $difference = $now->getTimestamp() - $user->lastCheckedAt;

                $user->health += ($difference / 60) * GAME_HEALTH_INCREASE_PER_MINUTE;
                if( $user->health > 100 )
                {
                    $user->health = 100;
                }

                $user->energy += ($difference / 60) * GAME_ENERGY_INCREASE_PER_MINUTE;
                if( $user->energy > 100 )
                {
                    $user->energy = 100;
                }

                $this->userModel->updateById($user->id, ["health" => $user->health, "energy" => $user->energy, "lastCheckedAt" => $now->format('Y-m-d H:i:s')]);
            }

            return true;
        }
    }
