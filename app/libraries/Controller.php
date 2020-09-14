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
