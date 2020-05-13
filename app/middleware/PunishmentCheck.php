<?php

  class PunishmentCheck extends Middleware
  {
    public function __construct(string ...$setup)
    {
      $this->setVariables(...$setup);
    }

    public function before(): bool
    {
      $this->punishmentModel = $this->model('Punishment');
      $this->permanentBan = $this->punishmentModel->getPermanentBanForUser($_SESSION["userId"]);
      $this->temporaryBan = $this->punishmentModel->getLastTemporaryBanForUser($_SESSION["userId"]);
      $now = new DateTime();
      
      if($this->permanentBan)
      {
          if($this->controller != "punishments" || $this->method != "permanent")
          {
              redirect(MIDDLEWARE_PUNISHMENTCHECK_PERMANENTREDIRECT);
          }

          return false;
      }
      elseif($this->temporaryBan
             && strtotime($this->temporaryBan->endsAt) > $now->getTimestamp())
      {
        if($this->controller != "punishments" || $this->method != "temporary")
        {
            redirect(MIDDLEWARE_PUNISHMENTCHECK_TEMPORARYREDIRECT);
        }

        return false;
      }
      elseif($this->controller == "punishments" && ($this->method == "temporary" || $this->method == "permanent"))
      {
          redirect(MIDDLEWARE_PUNISHMENTCHECK_NOPUNISHMENTREDIRECT);
      }

      return true;
    }
  }
