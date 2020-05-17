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
        // In prison
        if($this->controller != "prisons"
           || ($this->controller == "prisons" && $this->method == "index"))
        {
            redirect('prison/inside');
        }
      }else
      {
        if($this->controller == "prisons" && $this->method != "index")
        {
            redirect('prison/index');
        }
      }

      return true;
    }
  }
