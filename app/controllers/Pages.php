<?php
    namespace App\Controllers;

    use App\Libraries\Controller as Controller;
    class Pages extends Controller 
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
            $this->data = [
                'title' => 'Rival Guns',
                'description' => 'The newest Crime Game'
            ];

            $this->view('pages/index', $this->data);
        }


        /**
         * 
         * 
         * About
         * @return Void
         * 
         * 
         */
        public function about (): void
        {
            $this->data = [
                'title' => 'About Us',
                'description' => 'App to share posts with other users'
            ];

            $this->view( 'pages/about', $this->data );
        }


        /**
         * 
         * 
         * Error404
         * @return Void
         * 
         * 
         */
        public function error404 (): void
        {
            $this->data = [
                'title' => 'Uh Oh...',
                'description' => 'Your page could not be found'
            ];

            $this->view( 'pages/about', $this->data );
        }


        /**
         * 
         * 
         * Error400
         * @return Void
         * 
         * 
         */
        public function error400 (): void
        {
            $this->data = [
                'title' => 'Uh Oh...',
                'description' => 'You made a bad request!'
            ];

            $this->view( 'pages/about', $this->data );
        }


        /**
         * 
         * 
         * Error500
         * @return Void
         * 
         * 
         */
        public function error500 (): void
        {
            $this->data = [
                'title' => 'Uh Oh...',
                'description' => 'Internal Server Error!',
                'errorView' => true
            ];

            $this->view( 'pages/about', $this->data );
        }
    }
