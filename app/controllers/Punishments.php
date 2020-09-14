<?php
    namespace App\Controllers;
    use App\Libraries\Controller as Controller;
    
    class Punishments extends Controller
    {
        
        public function __construct()
        {
            $this->userModel = $this->model('User');
            $this->adminRightModel = $this->model('AdminRight');
            $this->adminRoleModel = $this->model('AdminRole');
            $this->conversationModel = $this->model('Conversation');
            $this->notificationModel = $this->model('Notification');

            // Set the sessions for the nav bar
            $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
            $this->data['user']->adminRights         = $this->adminRoleModel->getRightNamesForRole($this->data['user']->adminRole);
            $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
            $this->data['user']->notifications = $this->notificationModel->getUnreadNotifications($_SESSION['userId']);
        }


        /**
         * 
         * 
         * permanentBan
         * @return Void
         * 
         * 
         */
        public function permanentBan (): void
        {
            echo "You've received a permanent ban.";
        }


        /**
         * 
         * 
         * temporaryBan
         * @return Void
         * 
         * 
         */
        public function temporaryBan (): void
        {
            echo "You've received a temporary ban.";
        }

    }