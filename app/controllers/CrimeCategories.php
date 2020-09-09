<?php
  class crimeCategories extends Controller
  {
    public function __construct()
    {
      $this->userModel = $this->model('User');
      $this->crimeCategoryModel = $this->model('CrimeCategory');
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
     * index
     */
    public function index($type)
    {
      if($type == "crimes")
      {
        $dbType = "Crimes";
      }
      elseif($type == "mafiajobs")
      {
        $dbType = "Mafia Jobs";
      }
      else 
      {
        redirect('profile');
      }
      $crimeCategories = $this->crimeCategoryModel->getByMainCategory($dbType);

      $user = &$this->data['user'];

      if($user->health < 5)
      {
        $this->data['lowHealthWarning'] = true;
      }
      if($user->energy < 5)
      {
        $this->data['lowEnergyWarning'] = true;
      }

      $this->data['crimeCategories'] = $crimeCategories;
      $this->data['title'] = ucfirst($dbType);

      $this->view('crimeCategories/index', $this->data);
    }
  }
