<?php
    namespace App\Libraries;
    /*
    * Base Controller
    * Loads the models and views
    */
    class Middleware 
    {
        protected $api;
        protected $controller;
        protected $method;
        

        /**
         * 
         * 
         * loadModel
         * @param String modelName
         * @return Object
         */
        protected function model(String $model): Object
        {
            $model = MODEL_NAMESPACE . $model;
            // Instatiate model
            return new $model();
        }



        /**
         * 
         * 
         * setVariables
         * @param String api
         * @param String controller
         * @param String method
         * @return Void
         * 
         * 
         */
        protected function setVariables(String $api, String $controller, String $method): Void
        {
            $this->api = $api;
            $this->controller = lcfirst($controller);
            $this->method = lcfirst($method);
        }



        /**
         * 
         * 
         * shouldRunMiddleware
         * @return bool shouldRun
         * 
         * 
         */
        public function shouldRunMiddleware (): bool
        {
            $middlewareName = (new \ReflectionClass($this))->getShortName();
            $shouldRun = MIDDLEWARE[$middlewareName]['runByDefault'];

            if(! isset(MIDDLEWARE[$middlewareName]['exceptions']) )
            {
                return $shouldRun;
            }

            $apiText = ($this->api == true) ? 'api/' : '';

            $controllerName = $apiText . $this->controller;
            $controllerMethodName = $apiText . $this->controller . '/' . $this->method;
            
            if( 
                in_array( $controllerName, MIDDLEWARE[$middlewareName]['exceptions']) 
                || in_array( $controllerMethodName , MIDDLEWARE[$middlewareName]['exceptions']) 
            ) {
                $shouldRun = !$shouldRun;
            }

            return $shouldRun;
        }


        /**
         * 
         * 
         * run sequenced middleware
         * @param String $stage - before or after
         * @return Bool ran
         * 
         * 
         */
        public function runSequencedMiddleware(string $stage): bool
        {
            $middlewareName = (new \ReflectionClass($this))->getShortName();
            if( isset(MIDDLEWARE[$middlewareName]["sequenced"]) )
            {
                foreach( MIDDLEWARE[$middlewareName]["sequenced"] as $sequenced )
                {
                    $sequenced = MIDDLEWARE_NAMESPACE . $sequenced;
                    $sequencedMiddleware = new $sequenced($this->api, $this->controller, $this->method);
          
                    if( method_exists($sequencedMiddleware, $stage) )
                    {            
                        $continue = $sequencedMiddleware->$stage();
                        
                        if( ! $continue )
                        {
                          return false;
                        }
                    }
                }
            }

            return true;
        }

    }
