<?php
    namespace App\Controllers\Api;
    use App\Libraries\REST_Controller as REST_Controller;
    
    class Notifications extends REST_Controller
    {
        public function __construct()
        {
            $this->defaultMethod = "getMessages";

            $this->userModel = $this->model('User');
            $this->notificationModel = $this->model('Notification');
        }


        /**
         * 
         * 
         * Read
         * @return String
         * 
         * 
         */

        public function read (): String
        {
            // Check for POST request
            // This is the only option
            if( $_SERVER['REQUEST_METHOD'] == 'POST' )
            {
                if( ! isset($_SESSION['userId']) )
                {
                    // If we don't have everything, return a bad request
                    $this->response( [], 400 );
                }

                $userId = $_SESSION['userId'];
                $data = [];
                $data["success"] = $this->notificationModel->readForUser( $userId );
                
                // Return the response
                $this->response( $data, 200 );
            }
            else {
                // Otherwise return error
                $this->response( [], 405 );
            }
        }
    }
