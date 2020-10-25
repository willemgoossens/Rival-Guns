<?php
  namespace App\Controllers;
  use App\Libraries\Controller as Controller;

  class Tests extends Controller
  {
    public function index()
    {
      date_default_timezone_set('America/Aruba');
      $now = new \DateTime('2020-10-25 21:49:10');
      $now->setTimezone(new \DateTimeZone('Europe/Brussels'));
      $time = clone $now;
      $time->setTimezone(new \DateTimeZone('Europe/London'));

      var_dump($now == $time);
      var_dump($now < $time);
      var_dump($now > $time);
      echo "<br> " . $now->format('Y-m-d H:i:s');
      echo "<br> " . $time->format('Y-m-d H:i:s');

      $array = [$now, new \DateTime('yesterday'), new \DateTime('tomorrow')];
      sort($array);

      echo variablePrint($array);
    }
  }
