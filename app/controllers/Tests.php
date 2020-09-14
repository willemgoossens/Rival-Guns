<?php
  namespace App\Controllers;
  use App\Libraries\Controller as Controller;

  class Tests extends Controller
  {
    public function index()
    {
      var_dump($this->userModel->getSingleById(2));
    }
  }
