<?php

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
        $this->userModel = $this->model('User');
        $user = $this->userModel->getSingleById($_SESSION["userId"]);

        $this->hospitalizationModel = $this->model('Hospitalization');
        $hospitalization = $this->hospitalizationModel->getSingleByUserId($_SESSION["userId"]);
        
        $now = new DateTime();

        if( $hospitalization )
        {
            $hos = $hospitalization;
            $hos->endTime = new DateTime($hos->createdAt);
            $hos->endTime->modify('+' . $hos->duration . ' seconds');
            
            if( $hos->endTime->getTimestamp() < $now->getTimestamp() )
            {
                $user->health += ($hos->duration / 60) * GAME_HEALTH_INCREASE_PER_MINUTE_HOSPITAL;
                if( $user->health > 100 )
                {
                    $user->health = 100;
                }

                $user->energy += ($hos->duration / 60) * GAME_ENERGY_INCREASE_PER_MINUTE_HOSPITAL;
                if( $user->energy > 100 )
                {
                    $user->energy = 100;
                }

                $this->userModel->updateById($user->id, ["health" => $user->health, "energy" => $user->energy, "lastHealthCheck" => $now->format('Y-m-d H:i:s')]);
                $this->hospitalizationModel->deleteById($hos->id);
            }
            else
            {
                if(
                    $this->controller != "hospitalizations" 
                    || $this->method != "hospitalized"
                ) {
                    redirect('hospitalized');
                }
            }

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

            $user->lastHealthCheck = strtotime($user->lastHealthCheck);
            $difference = $now->getTimestamp() - $user->lastHealthCheck;

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

            $this->userModel->updateById($user->id, ["health" => $user->health, "energy" => $user->energy, "lastHealthCheck" => $now->format('Y-m-d H:i:s')]);
        }

        return true;
    }
  }
