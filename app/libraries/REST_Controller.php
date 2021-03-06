<?php
    namespace App\Libraries;
    
    class REST_Controller 
    {
        public $defaultMethod = "index";
        

         /**
         * 
         * 
         * __get
         * @param String $name
         * @return Object
         * 
         * 
         */
        public function __get(String $name): Object
        {
            if(substr($name, -5) == "Model")
            {
                $modelName = substr($name, 0, -5);
                $model = $this->model($modelName);

                $this->$name = $model;
                return $this->$name;
            }

            throw new \Exception("Uh Oh! " . $name . " is not a real variable for this class (" . get_class($this) . ")" , 1);
        }

 
        /**
         * 
         * 
         * model
         * @param String model
         * @return Object
         * 
         * 
         */
        public function model (string $model): Object
        {
          $model = MODEL_NAMESPACE . $model;
          return new $model();
        }


        /**
         * 
         * 
         * Respondse
         * @param Array data
         * @param Int status [Optional]
         * @return Void
         */
        public function response (array $data, int $status = 200): Void
        {
            header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
            // If the response array isn't empty encode the json_array, otherwise dont
            if( ! empty($data) )
            {
                die(json_encode($data));
            }
            else
            {
                die;
            }
        }


        /**
         * 
         * 
         * requestStatus
         * @param Int code
         * @return String Status
         * 
         * 
         */
        private function requestStatus (Int $code): String
        {
            $status = [
                200 => 'OK',
                400 => 'Bad Request',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                500 => 'Internal Server Error',
                535 => 'Authentication Failed'
            ];
            // Check if the error code exists in here
            // Otherwise return 500
            return ($status[$code]) ? $status[$code] : $status[500];
        }
    }
