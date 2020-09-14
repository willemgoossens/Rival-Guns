<?php
    /*
    * App Core Class
    * Creates URL & loads core controller
    * URL FORMAT - /controller/method/params
    */
    class Core 
    {
        protected $currentController = 'Pages';
        protected $currentMethod = 'index';
        protected $params = [];

        private $api = false;
        private $apiText = "";

        public function __construct()
        {
            $url = $this->getUrl();

            // Check if it is a request to the API
            if(
                ! empty($url) 
                && $url[0] == 'api'
            ) {
                $this->api = true;
                $this->apiText = "api/";

                header("Access-Control-Allow-Orgin: *");
                header("Access-Control-Allow-Methods: *");
                header("Content-Type: application/json");

                array_shift( $url );
            }
            // If exists, set as controller
            $this->currentController = isset($url[0]) ? ucwords($url[0]) : $this->currentController;

            if( ! file_exists(APPROOT . '/controllers/' . $this->apiText . $this->currentController . '.php') )
            {
                $this->error("404");
            }
            // Unset The $currentController
            unset( $url[0] );
            // Check for second part of url; the method
            // Require the controller
            require_once APPROOT . '/controllers/' . $this->apiText . $this->currentController . '.php';

            // Check for second part of url
            if( isset($url[1]) )
            {
                // Check to see if method exists in controller
                // If the method does indeed exist, replace it
                if( method_exists($this->currentController, $url[1]) )
                {
                    $this->currentMethod = $url[1];
                    // Unset 1 index
                    unset($url[1]);
                }
                elseif( method_exists($this->currentController, 'index') )
                {
                    $this->currentMethod = 'index';
                }
                else
                {          
                    $this->error("404");
                }
            }
            else
            {
                // Check to see if method exists in controller
                // If the method does indeed exist, replace it
                if( method_exists($this->currentController, 'index') )
                {
                    $this->currentMethod = 'index';
                }
                else
                {          
                    $this->error("404");
                }
            }
            
            $this->loadBeforeMiddleware();

            $this->currentController = new $this->currentController();

            // Do some extra filtering for the Get Parameters (they might be used as db input)
            // And assign them
            $url = $url ? array_values($url) : [];
            $this->params = filter_var_array($url, FILTER_SANITIZE_STRING);

            // We need to make sure we get the required amount of GET variables
            // For this we use Reflection
            // https://www.php.net/manual/en/reflectionfunctionabstract.getnumberofrequiredparameters.php
            $r  = new \ReflectionMethod($this->currentController, $this->currentMethod);
            if( $r->getNumberOfRequiredParameters() > count($this->params) )
            {
                $this->error(400);
            }

            // Call a callback with array of params
            call_user_func_array( [$this->currentController, $this->currentMethod], $this->params );

            $this->loadAfterMiddleware();
        }

        /**************************************
        *
        *
        * GET THE URL, SPLIT AND SANITIZE
        *
        *
        ***************************************/
        public function getUrl(): ?Array
        {
            if( isset($_GET['url']) )
            {
                $url = filter_var($_GET['url'], FILTER_SANITIZE_URL);

                // Routing
                $url = $this->routing($url);
                
                $url = explode('/', $url);
                return $url;
            }
        }


        /**
        *
        *
        * IF NECESSARY, RETURN AN ERROR
        * @param Int code
        * @return Void
        *
        *
        */
        public function error (Int $code): Void
        {
            header( ERROR_PAGES[$code]['api'] );

            if( ! $this->api )
            {
                redirect( ERROR_PAGES[$code]['page'] );
            }
        }


        /**
        *
        *
        * LOAD ALL THE MIDDLEWARE THAT SHOULD BE EXECUTED BEFORE THE CONTROLLER
        * @return Void
        *
        *
        */
        public function loadBeforeMiddleware(): Void
        {
            foreach( MIDDLEWARE as $middlewareName => $executionUrls )
            {
                require_once APPROOT . "/middleware/" . $middlewareName . ".php";

                $this->middleware[$middlewareName] = new $middlewareName($this->api, $this->currentController, $this->currentMethod);

                if( 
                    method_exists($middlewareName, "before")
                    && $this->middleware[$middlewareName]->shouldRunMiddleware() 
                ) {            
                    $continue = $this->middleware[$middlewareName]->before();
                    
                    if( $continue )
                    {
                        $this->middleware[$middlewareName]->runSequencedMiddleware("before");
                    }
                }
            }

            ob_start();
        }

        /**
        *
        *
        * LOAD ALL THE MIDDLEWARE THAT SHOULD BE EXECUTED AFTER THE CONTROLLER
        * @return Void
        *
        *
        */
        public function loadAfterMiddleware(): Void
        {
            foreach( MIDDLEWARE as $middlewareName => $executionUrls )
            {
                if( 
                    method_exists($middlewareName, "after")
                    && $this->middleware[$middlewareName]->shouldRunMiddleware()
                ) {            
                    $continue = $this->middleware[$middlewareName]->after($this);
                    
                    if( $continue )
                    {
                        $this->middleware[$middlewareName]->runSequencedMiddleware("after");
                    }
                }
            }

            ob_end_flush();
        }


        /**
        *
        *
        * RETURN THE DATA FROM THE CONTROLLER
        * @return Array data
        *
        *
        */
        public function returnControllerData (): Array
        {
            return $this->currentController->data;
        }

        /**
        *
        *
        * Routing
        * @param String Url
        * @return String Broken Url
        *
        *
        */
        public function routing(String $url): String
        {
            $output = $url;

            foreach( ROUTING as $key => $value )
            {
                $key = ltrim($key, "/");
                $key = rtrim($key, "/");
                $key = str_replace("/", "\/", $key);

                if( preg_match('/^' . $key . '$/', $url) )
                {
                    $output = preg_replace('/^' . $key . '$/', $value, $url);
                    break;
                }
            }
            $output = rtrim($output, "/");

            return $output;
        }

    }
