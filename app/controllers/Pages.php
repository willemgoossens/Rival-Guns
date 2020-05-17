<?php
  class Pages extends Controller 
  {

    public function __construct()
    {
      $this->userModel = $this->model('User');
      $this->adminRoleModel = $this->model('AdminRole');
      $this->conversationModel = $this->model('Conversation');
    }

    public function index(){

      $this->data = [
        'title' => 'Rival Guns',
        'description' => 'The newest Crime Game'
      ];

      $this->view('pages/index', $this->data);
    }

    public function about(){
      $this->data = [
        'title' => 'About Us',
        'description' => 'App to share posts with other users'
      ];

      if($this->userModel->isLoggedIn())
      {
        // Set the sessions for the nav bar
        $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
        $this->data['user']->adminRights         = $this->adminRoleModel->getRightNamesForRole($this->data['user']->adminRole);
        $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
      }

      $this->view('pages/about', $this->data);
    }

    public function error404(){
      $this->data = [
        'title' => 'Uh Oh...',
        'description' => 'Your page could not be found'
      ];

      $this->view('pages/about', $this->data);
    }

    public function error400(){
      $this->data = [
        'title' => 'Uh Oh...',
        'description' => 'You made a bad request!'
      ];

      $this->view('pages/about', $this->data);
    }

    public function error500(){
      $this->data = [
        'title' => 'Uh Oh...',
        'description' => 'Internal Server Error!',
        'errorView' => true
      ];

      $this->view('pages/about', $this->data);
    }
  }
