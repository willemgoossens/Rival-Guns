<?php
    namespace App\Controllers;
    use App\Libraries\Controller as Controller;
    
    class Prisons extends Controller
    {
        public function __construct()
        {
            // Set the sessions for the nav bar
            $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
            $this->data['user']->adminRights         = $this->adminRoleModel->getRightNamesForRole($this->data['user']->adminRole);
            $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
            $this->data['user']->notifications = $this->notificationModel->getUnreadNotifications($_SESSION['userId']);
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