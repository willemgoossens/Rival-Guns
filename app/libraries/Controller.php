<?php
    namespace App\Libraries;
    /*
    * Base Controller
    * Loads the models and views
    */
    class Controller 
    {
        public $data = [];
        
        
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
         * Model
         * @param String Model
         * @return Object class
         * 
         * 
         */
        public function model (String $model): Object
        {
            $model = MODEL_NAMESPACE . $model;
            return new $model();
        }

        
        /**
         * 
         * 
         * View
         * @param String view
         * @param Array data
         * @return Void
         */
        public function view (String $view, Array $data = []): Void
        {
            // Check for view file
            if( file_exists('../app/views/' . $view . '.php') )
            {
                require_once '../app/views/' . $view . '.php';
            } 
            else 
            {
                // View does not exist
                die('View does not exist');
            }
        }
    }
