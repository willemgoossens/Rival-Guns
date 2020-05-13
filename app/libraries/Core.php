<?php
  /*
   * App Core Class
   * Creates URL & loads core controller
   * URL FORMAT - /controller/method/params
   */
  class Core {
    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $interface = 'interface';
    protected $params = [];

    private $api = false;
    private $apiText = "";

    public function __construct(){
      //print_r($this->getUrl());

      $url = $this->getUrl();

      // Check if it is a request to the API
      if($url[0] == 'api')
      {
        $this->api = true;
        $this->apiText = "api/";
        $this->interface = "api";

        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        array_shift($url);
      }
      // If exists, set as controller
      $this->currentController = isset($url[0]) ? ucwords($url[0]) : $this->currentController;

      if(!file_exists(APPROOT . '/controllers/' . $this->apiText . $this->currentController . '.php'))
      {
        $this->error("404");
      }
      // Unset The $currentController
      unset($url[0]);
      // Check for second part of url; the method
      // Require the controller
      require_once APPROOT . '/controllers/' . $this->apiText . $this->currentController . '.php';
      // Instantiate controller class
      $this->currentController = new $this->currentController;

      // If this controllor has a defaultMethod that is different from 'index'
      // Set it as the currentMethod (as a failsafe)
      if(!method_exists($this->currentController, $this->currentController->defaultMethod)){
        // If 'defaultMethod' has been set, we change the
        throw new \Exception("Yo, the defaultMethod of this Controller (" . get_class($this->currentController) . ") doesn't exist, please solve this issue first.", 1);
      }
      else
      {
        $this->currentMethod = $this->currentController->defaultMethod;
      }

      // Check for second part of url
      if(isset($url[1])){
        // Check to see if method exists in controller
        // If the method does indeed exist, replace it
        if(method_exists($this->currentController, $url[1])){
          $this->currentMethod = $url[1];
          // Unset 1 index
          unset($url[1]);
        }
      }

      $this->loadBeforeMiddleware();

      // Do some extra filtering for the Get Parameters (they might be used as db input)
      // And assign them
      $url = $url ? array_values($url) : [];
      $this->params = filter_var_array($url, FILTER_SANITIZE_STRING);

      // We need to make sure we get the required amount of GET variables
      // For this we use Reflection
      // https://www.php.net/manual/en/reflectionfunctionabstract.getnumberofrequiredparameters.php
      $r  = new \ReflectionMethod($this->currentController, $this->currentMethod);
      if($r->getNumberOfRequiredParameters() > count($this->params))
      {
        $this->error(400);
      }

      // Call a callback with array of params
      call_user_func_array([$this->currentController, $this->currentMethod], $this->params);

      $this->loadAfterMiddleware();
    }

    /**************************************
    *
    *
    * GET THE URL, SPLIT AND SANITIZE
    *
    *
    ***************************************/
    public function getUrl()
    {
      if(isset($_GET['url']))
      {
        $url = filter_var($_GET['url'], FILTER_SANITIZE_URL);

        // Routing
        $url = $this->routing($url);
        
        $url = explode('/', $url);
        return $url;
      }
    }

    /**************************************
    *
    *
    * IF NECESSARY, RETURN AN ERROR
    * @PARAM: error code
    *
    *
    ***************************************/
    public function error($code)
    {
      header(ERROR_PAGES[$code]['api']);
      if(!$this->api)
      {
        redirect(ERROR_PAGES[$code]['page']);
      }
    }

    /**************************************
    *
    *
    * LOAD ALL THE MIDDLEWARE THAT SHOULD BE EXECUTED BEFORE THE CONTROLLER
    *
    *
    ***************************************/
    public function loadBeforeMiddleware()
    {
      foreach(MIDDLEWARE as $middlewareName => $executionUrls)
      {
        require_once APPROOT . "/middleware/" . $middlewareName . ".php";

        $this->middleware[$middlewareName] = new $middlewareName($this->interface, get_class($this->currentController), $this->currentMethod);

        if(method_exists($middlewareName, "before")
           && $this->middleware[$middlewareName]->shouldRunMiddleware())
        {            
          $continue = $this->middleware[$middlewareName]->before();
          if($continue)
          {
            $this->middleware[$middlewareName]->runSequencedMiddleware("before");
          }
        }
      }

      ob_start();
    }

    /**************************************
    *
    *
    * LOAD ALL THE MIDDLEWARE THAT SHOULD BE EXECUTED AFTER THE CONTROLLER
    *
    *
    ***************************************/
    public function loadAfterMiddleware()
    {

      foreach(MIDDLEWARE as $middlewareName => $executionUrls)
      {
        if(method_exists($middlewareName, "after")
           && $this->middleware[$middlewareName]->shouldRunMiddleware())
        {            
          $continue = $this->middleware[$middlewareName]->after($this);
          if($continue)
          {
            $this->middleware[$middlewareName]->runSequencedMiddleware("after");
          }
        }
      }

      ob_end_flush();
    }

    /**************************************
    *
    *
    * RETURN THE DATA FROM THE CONTROLLER
    * This is needed for the Debugger Toolbar
    *
    *
    ***************************************/
    public function returnControllerData()
    {
      return $this->currentController->data;
    }

    /**************************************
    *
    *
    * ROUTING
    * urls
    *
    *
    ***************************************/
    public function routing($url)
    {
      $output = $url;

      foreach(ROUTING as $key => $value)
      {
        $key = ltrim($key, "/");
        $key = rtrim($key, "/");
        $key = str_replace("/", "\/", $key);

        if(preg_match('/^' . $key . '$/', $url))
        {
          $output = preg_replace('/^' . $key . '$/', $value, $url);
          break;
        }
      }
      $output = rtrim($output, "/");

      return $output;
    }

  }
