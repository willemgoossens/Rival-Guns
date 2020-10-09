<?php
  namespace App\Controllers;
  use App\Libraries\Controller as Controller;

  class Tests extends Controller
  {
    public function index()
    {
      $datetime = new \DateTime('2020-10-02 13:00:00');
      $this->hospitalizationModel->calculateHealthAndEnergyForUser(1, $datetime);
    }
  }
