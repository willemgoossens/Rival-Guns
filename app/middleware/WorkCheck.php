<?php

    class WorkCheck extends Middleware
    {
        public function __construct(string ...$setup)
        {
            $this->setVariables(...$setup);
        }

        public function before(): bool
        {
            $this->userModel = $this->model('User');
            $this->conversationModel =$this->model('Conversation');
            $this->user = $this->userModel->getSingleById($_SESSION["userId"], 'id', 'workingUntil', 'charismaSkills', 'name', 'bank');
            
            $now = new DateTime();

            if( isset($this->user->workingUntil)
                && strtotime($this->user->workingUntil) <= $now->getTimestamp()
            )
            {
                $max = ceil( ($this->user->charismaSkills + 1) / 100 );
                
                if( $max > 5 )
                {
                    $max = 5;
                }

                $amountOfVacuums = rand( 0, $max );
                $earnings = $amountOfVacuums * 40;

                $this->user->bank += $earnings;
                $this->user->charismaSkills += $amountOfVacuums;
                
                $updateArray = 
                [
                    'bank' => $this->user->bank,
                    'workingUntil' => null,
                    'charismaSkills' => $this->user->charismaSkills
                ];

                $this->userModel->updateById($this->user->id, $updateArray);

                $message = 
                "Dear " . ucfirst($this->user->name) . ",\r\n\r\nThank you for working with Harry\'s Hoovers!\r\nBy selling " . $amountOfVacuums . " vacuum cleaner(s), you made $" . $earnings . ".\r\nYou also earned " . $amountOfVacuums . " charisma points. The money has been deposited in your bank account.\r\n\r\nKind regards,\r\nHarry";

                if( $amountOfVacuums == 0 )
                {
                    $message = "Goddamnit " . ucfirst($this->user->name) . ",\r\n\r\nYou sold no vacuum cleaners at all.\r\nHow hard can it be?\r\n\r\nHarry";
                }

                // Create a new conversation
                $insertConversationData = [
                  'noReply' => true,
                  'noReplySender' => 'Harry\'s Hoovers',
                  'subject' => 'Work report'
                ];

                $toIds = [
                  $this->user->id
                ];

                $this->conversationModel->addConversation($insertConversationData, $message, $toIds);
            }

            return true;
        }
    }
