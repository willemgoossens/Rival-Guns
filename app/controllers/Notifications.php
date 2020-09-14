<?php
    namespace App\Controllers;
    use App\Libraries\Controller as Controller;
    
    class Notifications extends Controller
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
         * Index
         * @param Int Page
         * @return Void
         * 
         * 
         */
        public function index ( Int $page = 1 ): Void
        {
            $user = &$this->data['user'];
            $this->data['user']->notifications = [];

            $this->notificationModel->deleteOldNotifications( $user->id );
            
            $limit = 15;
            $offset = ( $page - 1 ) * $limit;

            $notifications = $this->notificationModel->limit( $limit )
                                                     ->offset( $offset )
                                                     ->orderBy( 'createdAt', 'DESC' )
                                                     ->getByUserId( $user->id );

            $this->notificationModel->readForNotificationIds( $notifications );

            $this->data['notificationsThisPage'] = $notifications;
            $this->data['page'] = $page;
            $this->data['totalNotifications'] = $this->notificationModel->countByUserId( $user->id );
            $this->data['notificationsPerPage'] = $limit;

            $this->view( 'notifications/index', $this->data );
        }

    }