<?php

  class PrisonCheck extends Middleware
  {
    public function __construct(string ...$setup)
    {
      $this->setVariables(...$setup);
    }

    public function before(): bool
    {
      $this->userModel = $this->model('User');
      $this->user = $this->userModel->getSingleById($_SESSION["userId"], 'inJailUntil');
      
      $now = new DateTime();

      if(strtotime($this->user->inJailUntil) > $now->getTimestamp())
      {
        if($this->controller != "prisons"
           || ($this->controller == "prisons" && $this->method == "index"))
        {
            redirect(MIDDLEWARE_PRISONCHECK_INPRISONREDIRECT);
        }
      }else
      {
        if($this->controller == "prisons" && $this->method != "index")
        {
            redirect(MIDDLEWARE_PRISONCHECK_OUTPRISONREDIRECT);
        }
      }

      return true;
    }
  }
