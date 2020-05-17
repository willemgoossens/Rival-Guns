<?php
  /*
   * Base Controller
   * Loads the models and views
   */
  class Middleware {
    protected $interface;
    protected $controller;
    protected $method;
    // Load model
    protected function model($model){
      // Require model file
      require_once APPROOT . '/models/' . $model . '.php';

      // Instatiate model
      return new $model();
    }

    protected function setVariables(string $interface, string $controller, string $method)
    {
      $this->interface  = strtolower($interface);
      $this->controller = strtolower($controller);
      $this->method     = strtolower($method);
    }

    public function shouldRunMiddleware()
    {
      
      if(isset(MIDDLEWARE[get_class($this)][$this->interface]))
      {
        $paths = MIDDLEWARE[get_class($this)][$this->interface];
      }
      else
      {
        return false;
      }

      if(isset($paths['except'][$this->controller]['except'])
        && in_array($this->method, $paths['except'][$this->controller]['except']))
      {
        if(!$paths['except'][$this->controller]["default"])
        {
          return true;
        }
      }
      elseif(isset($paths['except'][$this->controller]))
      {
        if($paths['except'][$this->controller]["default"])
        {
          return true;
        }
      }elseif($paths["default"] === true)
      {
        return true;
      }

      return false;
    }



    /**
     * run sequenced middleware
     * @param string $stage - before or after
     */
    public function runSequencedMiddleware(string $stage)
    {
      if(isset(MIDDLEWARE[get_class($this)]["sequenced"]))
      {
        foreach(MIDDLEWARE[get_class($this)]["sequenced"] as $sequenced)
        {
          require_once APPROOT . "/middleware/" . $sequenced . ".php";
  
          $sequencedMiddleware = new $sequenced($this->interface, $this->controller, $this->method);
  
          if(method_exists($sequencedMiddleware, $stage))
          {            
            $continue = $sequencedMiddleware->$stage();
            
            if(! $continue)
            {
              return false;
            }
          }
        }
      }

      return true;
    }

  }
