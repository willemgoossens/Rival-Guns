<?php
    namespace App\Libraries;
    /*
    * App Core Class
    * Creates URL & loads core controller
    * URL FORMAT - /controller/method/params
    */
    class Core 
    {
        protected $currentController = 'Pages';
        protected $currentControllerWithNamespace = CONTROLLER_NAMESPACE . 'Pages';
        protected $instantiatedController;
        protected $currentMethod = 'index';
        protected $params = [];

        private $api = false;

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

                $this->currentController = isset($url[0]) ? lcfirst($url[0]) : $this->currentController;
                $this->currentControllerWithNamespace = API_CONTROLLER_NAMESPACE . $this->currentController;
            }
            else
            {                
                $this->currentController = isset($url[0]) ? lcfirst($url[0]) : $this->currentController;
                $this->currentControllerWithNamespace = CONTROLLER_NAMESPACE . $this->currentController;
            }
            
            if( ! class_exists($this->currentControllerWithNamespace) )
            {
                $this->error("404");
            }
            else
            {
                unset($url[0]);
            }

            $this->currentMethod = isset($url[1]) ? lcfirst($url[1]) : $this->currentMethod;
            if( method_exists($this->currentControllerWithNamespace, $this->currentMethod) )
            {
                unset($url[1]);
            }
            elseif( method_exists($this->currentControllerWithNamespace, 'index') )
            {
                $this->currentMethod = 'index';
            }
            else
            {
                $this->error("404");
            }

            $url = $url ? array_values($url) : [];
            $this->params = filter_var_array($url, FILTER_SANITIZE_STRING);
            

            $this->loadBeforeMiddleware();

            $this->instantiatedController = new $this->currentControllerWithNamespace;
            call_user_func([$this->instantiatedController, 'init']);

            $r  = new \ReflectionMethod($this->currentControllerWithNamespace, $this->currentMethod);
            if( $r->getNumberOfRequiredParameters() > count($this->params) )
            {
                $this->error("400");
            }

            if( ! empty($this->params) )
            {
                $rParams = $r->getParameters();
                foreach( $this->params as $key => &$urlVar )
                {
                    $type = $rParams[$key]->getType()->getName();
                    if( strtolower( $type ) == 'int')
                    {
                        if( ! ctype_digit($urlVar) )
                        {
                            $this->error("400");
                        }
                        else
                        {
                            $urlVar = (int) $urlVar;
                        }
                    }
                }
            }
            // Call a callback with array of params
            call_user_func_array( [$this->instantiatedController, $this->currentMethod], $this->params );

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

            return null;
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
                $middlewareName = MIDDLEWARE_NAMESPACE . $middlewareName;
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
