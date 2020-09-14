<?php
    class Pages extends Controller 
    {
        public function __construct ()
        {
            $this->userModel = $this->model('User');
            $this->adminRoleModel = $this->model('AdminRole');
            $this->conversationModel = $this->model('Conversation');
            $this->notificationModel = $this->model('Notification');
        }

        
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

            if( $this->userModel->isLoggedIn() )
            {
                // Set the sessions for the nav bar
                $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
                $this->data['user']->adminRights         = $this->adminRoleModel->getRightNamesForRole($this->data['user']->adminRole);
                $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
                $this->data['user']->notifications = $this->notificationModel->getUnreadNotifications($_SESSION['userId']);
            }

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
