<?php
  error_reporting(E_ALL);

  // Load Config
  require_once 'config/system.php';
  require_once 'config/routing.php';
  require_once 'config/middleware.php';
  require_once 'config/game.php';

  // Load Helpers
  require_once 'helpers/debugging_helper.php';
  require_once 'helpers/editor_helper.php';
  require_once 'helpers/form_helper.php';
  require_once 'helpers/pagination_helper.php';
  require_once 'helpers/session_helper.php';
  require_once 'helpers/url_helper.php';


  if(ENVIRONMENT == "production") 
  {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
  } 
  else 
  {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
  }

  function exceptionHandler($exception)
  {
    if(ENVIRONMENT == 'production')
    {
      redirect('pages/error500');
    }else
    {
      echo "<b>Error:</b>" . $exception->getMessage() . " on line " . $exception->getLine() . " in file " . $exception->getFile();
      echo "<pre>", print_r($exception->getTrace()) ,"</pre>";
      die;
    }
  }
  set_exception_handler('exceptionHandler');



  // Autoload Core Libraries
  spl_autoload_register(function($className){
    require_once 'libraries/' . $className . '.php';
  });
