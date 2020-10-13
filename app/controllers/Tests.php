<?php
  namespace App\Controllers;
  use App\Libraries\Controller as Controller;

  class Tests extends Controller
  {
    public function index()
    {
      $this->futureImprisonmentModel->finishDueFutureImprisonmentsForUserAndTime( 2 , new \DateTime );
    }
  }
