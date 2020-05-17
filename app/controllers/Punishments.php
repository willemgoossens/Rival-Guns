<?php
    class Punishments extends Controller
    {
        
        public function __construct()
        {
            $this->userModel = $this->model('User');
            $this->adminRightModel = $this->model('AdminRight');
            $this->adminRoleModel = $this->model('AdminRole');
            $this->conversationModel = $this->model('Conversation');

            // Set the sessions for the nav bar
            $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
            $this->data['user']->adminRights         = $this->adminRoleModel->getRightNamesForRole($this->data['user']->adminRole);
            $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
        }



        /**
         * permanent Ban
         */
        public function permanent ()
        {
            echo "You've received a permanent ban.";
        }



        /**
         * temporary ban
         */
        public function temporary ()
        {
            echo "You've received a temporary ban.";
        }

    }