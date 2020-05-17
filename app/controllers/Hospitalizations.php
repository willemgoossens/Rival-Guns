<?php
    class Hospitalizations extends Controller
    {

        public function __construct()
        {
            $this->userModel = $this->model('User');
            $this->adminRightModel = $this->model('AdminRight');
            $this->adminRoleModel = $this->model('AdminRole');
            $this->conversationModel = $this->model('Conversation');
            $this->hospitalizationModel = $this->model('Hospitalization');

            // Set the sessions for the nav bar
            $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
            $this->data['user']->adminRights         = $this->adminRoleModel->getRightNamesForRole($this->data['user']->adminRole);
            $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
        }

        public function hospitalized ()
        {
            $user = &$this->data['user'];

            $hospitalization = $this->hospitalizationModel->getSingleByUserId($user->id);

            $endDate = new DateTime($hospitalization->createdAt);
            $endDate->modify('+' . $hospitalization->duration . ' seconds');

            $now = new DateTime();

            $this->data['interval'] =  $endDate->getTimestamp() - $now->getTimestamp();

            $this->view('hospitalizations/hospitalized', $this->data);
        }

    }