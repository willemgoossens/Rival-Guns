<?php
    namespace App\Controllers;
    use App\Libraries\Controller as Controller;
    
    class Prisons extends Controller
    {        
        /**
         * 
         * 
         * Index
         * @return Void
         * 
         * 
         */
        public function index (): void
        {
            echo "welcome to the prison page";
        }


        /**
         * 
         * 
         * Inside
         * @return Void
         * 
         * 
         */
        public function inside (): void
        {
            echo "You're in prison";
        }

    }