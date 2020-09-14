<?php

    class WorkCheck extends Middleware
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
            $this->notificationModel = $this->model('Notification');
            $this->user = $this->userModel->getSingleById($_SESSION["userId"], 'id', 'workingUntil', 'charismaSkills', 'name', 'bank');
            
            $now = new DateTime();

            if( 
                isset($this->user->workingUntil)
                && strtotime($this->user->workingUntil) <= $now->getTimestamp()
            ) {
                $max = ceil( ($this->user->charismaSkills + 1) / 100 );    

                if( $max > 5 )
                {
                    $max = 5;
                }            

                $amountOfVacuums = rand( 0, $max );
                $earnings = $amountOfVacuums * 40;


                $notificationBody = "You have successfully sold " . $amountOfVacuums . " vacuum cleaners and made <strong>&euro;" . $earnings . "</strong>";
                $notificationClass = "alert-success";

                if( $amountOfVacuums == 0 )
                {
                    $notificationBody = "You have sold no vacuum cleaners at all...";
                    $notificationClass = "alert-warning";
                }
                elseif( $amountOfVacuums == 1 )
                {
                    $notificationBody = "You have successfully sold " . $amountOfVacuums . " vacuum cleaner and made <strong>&euro;" . $earnings . "</strong>";
                }

                // Create a new conversation
                $insertNotificationData = [
                    'userId' => $this->user->id,
                    'class' => $notificationClass,
                    'body' => $notificationBody,
                    'link' => '/locations/hoovers',
                    'createdAt' => $this->user->workingUntil
                ];

                $this->notificationModel->insert($insertNotificationData);

                $this->user->bank += $earnings;
                $this->user->charismaSkills += $amountOfVacuums;
                
                $updateArray = 
                [
                    'bank' => $this->user->bank,
                    'workingUntil' => null,
                    'charismaSkills' => $this->user->charismaSkills
                ];

                $this->userModel->updateById($this->user->id, $updateArray);
            }

            return true;
        }
    }
