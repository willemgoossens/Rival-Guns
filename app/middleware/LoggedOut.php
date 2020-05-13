<?php

  class LoggedOut extends Middleware
  {
    public function __construct(string ...$setup)
    {
      $this->setVariables(...$setup);
    }

    public function before(): bool
    {
      $this->userModel = $this->model('User');

      if(!$this->userModel->isLoggedIn())
      {
        return true;
      }else
      {
        header("HTTP/1.1 401 Unauthorized");
        redirect(MIDDLEWARE_LOGGEDOUT_REDIRECT);
      }
    }
  }
