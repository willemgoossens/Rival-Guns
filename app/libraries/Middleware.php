<?php
    /*
    * Base Controller
    * Loads the models and views
    */
    class Middleware 
    {
        protected $api;
        protected $controller;
        protected $method;
        // Load model
        protected function model($model)
        {
            // Require model file
            require_once APPROOT . '/models/' . $model . '.php';

            // Instatiate model
            return new $model();
        }



        /**
         * setVariables
         * @param string api
         * @param string controller
         * @param string method
         */
        protected function setVariables(string $api, string $controller, string $method)
        {
            $this->api = $api;
            $this->controller = lcfirst($controller);
            $this->method = lcfirst($method);
        }



        /**
         * shouldRunMiddleware
         * @return bool shouldRun
         */
        public function shouldRunMiddleware(): bool
        {
            $shouldRun = MIDDLEWARE[get_class($this)]['runByDefault'];

            if( !isset(MIDDLEWARE[get_class($this)]['exceptions']) )
            {
                return $shouldRun;
            }

            $apiText = ($this->api == true) ? 'api/' : '';

            $controllerName = $apiText . $this->controller;
            $controllerMethodName = $apiText . $this->controller . '/' . $this->method;
            
            if( in_array( $controllerName, MIDDLEWARE[get_class($this)]['exceptions']) 
                || in_array( $controllerMethodName , MIDDLEWARE[get_class($this)]['exceptions']) )
            {
                $shouldRun = !$shouldRun;
            }

            return $shouldRun;
        }



        /**
         * run sequenced middleware
         * @param string $stage - before or after
         * @return bool ran
         */
        public function runSequencedMiddleware(string $stage): bool
        {
            if(isset(MIDDLEWARE[get_class($this)]["sequenced"]))
            {
                foreach(MIDDLEWARE[get_class($this)]["sequenced"] as $sequenced)
                {
                    require_once APPROOT . "/middleware/" . $sequenced . ".php";
          
                    $sequencedMiddleware = new $sequenced($this->api, $this->controller, $this->method);
          
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
