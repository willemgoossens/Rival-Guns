<?php
  class Punishment extends Model
   {

    public function __construct()
    {
      $this->db = new Database;
      $this->setTableName('punishments');
    }

    /***************************************
    *
    * get latest temporary ban for user
    * @param: userId
    *
    ****************************************/
    public function getLastTemporaryBanForUser(int $userId) {
      $punishment = $this->orderBy("endsAt", "DESC")
                         ->limit(1)
                         ->getSingleByUserIdAndPunishmentType($userId, 'temporaryBan');

      if($punishment) 
      {
        return $punishment;
      } 
      else 
      {
        return false;
      }
    }

    /***************************************
    *
    * get latest temporary ban for user
    * @param: userId
    *
    ****************************************/
    public function getPermanentBanForUser(int $userId)
    {
      $punishment = $this->getSingleByUserIdAndPunishmentType($userId, 'permanentBan');

      if($punishment) 
      {
        return $punishment;
      } 
      else 
      {
        return false;
      }
    }

    /***************************************
    *
    * get punishments for users
    * @param: userId
    * @param: punishmentType
    * @param: still active
    *
    ****************************************/
    public function getPunishmentForUser(int $userId, ?string $punishmentType = NULL, bool $stillActive): array
    {
      $this->db->query("SELECT *
                        FROM punishments
                        WHERE ");
    }

    /**
     * this function validatest the input of the punishment form
     * @param  array $input An array with all the input (punishment, justification and datepicker)
     */
    public function validatePunishmentFormInput(array $input)
    {
      if(empty($input['punishment']))
      {
        return null;
      }

      if(empty($input['justification']))
      {
        $error = true;

        $input['justificationError'] = "You have not entered a justification message.";
      }
      else
      {
        $input['justificationError'] = false;
      }

      if($input['punishment'] == "temporaryBan")
      {
        if(empty($input['datePicker']))
        {
          $error = true;

          $input['datePickerError'] = "You have not entered an end date for the ban.";
        }
        else
        {
          $date = DateTime::createFromFormat('m/d/Y h:i A', $input['datePicker']);

          if(!$date || new DateTime() >= $date)
          {
            $error = true;

            $input['datePickerError'] = 'You have entered an invalid date.';
          }
          else
          {
            $input['datePickerError'] = false;
          }
        }
      }
      else
      {
        $input['datePickerError'] = null;
      }

      // Check if the user already has a temporary ban, and perhaps add up the times
      if($input['punishment'] == "temporaryBan")
      {
        $now = new DateTime();
        $datePicker = new DateTime($input['datePicker']);
        
        $lastTempBan = $this->getLastTemporaryBanForUser($input['userId']);

        if($lastTempBan)
        {
          $lastTempBan->endsAt = new DateTime($lastTempBan->endsAt);
          if($lastTempBan->endsAt > $now)
          {
            $interval = $now->diff($lastTempBan->endsAt);
            $datePicker->add($interval);
          }
        }

        $input['datePicker'] = $datePicker->format("Y-m-d H:i:s");
      }

      return $input;
    }
  }
