<?php
  class Posts extends Controller 
  {
    public function __construct() 
    {
        $this->userModel = $this->model('User');
        $this->postModel = $this->model('Post');
        $this->adminRoleModel = $this->model('AdminRole');
        $this->conversationModel = $this->model('Conversation');

        // Set the sessions for the nav bar
        $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
        $this->data['user']->adminRights         = $this->adminRoleModel->getRightNamesForRole($this->data['user']->adminRole);
        $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
    }

    public function index(int $page = null) 
    {
      // Get the total post count
      $postsCount = $this->postModel->count();
      // Make sure it's higher than 1
      if($page < 1){
        $page = 1;
      }
      // The page should never be empty
      // So make sure the page nr can't be too high
      // We use 5 posts per page
      $postsLimit = 5;
      if($page * 5 - $postsCount > $postsLimit){
        $page = 1;
      }
      // Now we calculate the first and
      $offset = ($page - 1) * $postsLimit;

      // Get Posts
      $posts = $this->postModel->orderBy("createdAt", "DESC")->limit($postsLimit)->offset($offset)->get();

      $this->data['posts'] = $posts;
      $this->data['page'] = $page;
      $this->data['casesPerPage'] = 5;
      $this->data['totalCases'] = $postsCount;
      $this->data['canAdd'] = in_array("AddPosts", $this->data['user']->adminRights) ? true : false;

      $this->view('posts/index', $this->data);
    }

    /************************
    *
    *
    * ADD
    * @PARAM: post id
    *
    *
    *************************/
    public function add()
    {
      // First check if the user has the rights to add posts
      $user = &$this->data["user"];

      if(!in_array("AddPosts", $this->data["user"]->adminRights))
      {
        redirect('posts');
      }

      $this->data['title'] = '';
      $this->data['body'] = '';
      $this->data['titleError'] = NULL;
      $this->data['bodyError'] = NULL;

      if($_SERVER['REQUEST_METHOD'] == 'POST')
      {
        // Sanitize POST array
        // First we Convert this stuff to Markdown
        $_POST['body'] = HTMLToMarkdown($_POST['body']);
        // Now filter out the remainder!
        // Note that we use filter_var_array here!
        $_POST = filter_var_array($_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $this->data['title'] = trim($_POST['title']);
        $this->data['body'] = trim($_POST['body']);

        // Validate the title
        if(empty($this->data['title']))
        {
          $this->data['titleError'] = 'Please enter title';
        }
        else
        {
          $this->data['titleError'] = false;
        }
        // Validate the body
        if(empty($this->data['body']))
        {
          $this->data['bodyError'] = 'Please enter body text';
        }
        else
        {
          $this->data['bodyError'] = false;
        }

        // make sure no errors
        if(empty($this->data['titleError'])
          && empty($this->data['bodyError'])
        ) {
          // Validated
          $insertData = [
            'title' => $this->data['title'],
            'body' => $this->data['body'],
            'userId' => $user->id
          ];

          if($this->postModel->insert($insertData))
          {
            flash('post_message', 'Your post has been added!');
            redirect('posts');
          }
          else
          {
            die('Something went wrong');
          }
        }
        else
        {
          $this->data['body'] = MarkdownToHTML($this->data['body']);
        }
      }

      $this->view('posts/add', $this->data);
    }

    /************************
    *
    *
    * SHOW
    * @PARAM: post id
    *
    *
    *************************/
    public function show(int $id){
      $post = $this->postModel->getSingleById($id);

      if(empty($post)){
        redirect('posts');
      }else{
        $poster = $this->userModel->getSingleById($post->userId);
        $post->body = MarkdownToHTML($post->body);
        $user = &$this->data['user'];

        if(in_array('OverrulePosts', $user->adminRights) ||
          ($_SESSION['userId'] == $poster->id && in_array('AddPosts', $user->adminRights))){
          $canEdit = true;
        }else{
          $canEdit = false;
        }

        $this->data['post'] = $post;
        $this->data['poster'] = $poster;
        $this->data['canEdit'] = $canEdit;

        $this->view('posts/show', $this->data);
      }
    }

    /************************
    *
    *
    * EDIT
    * @PARAM: post id
    *
    *
    *************************/
    public function edit(int $id){
      // Get existing post
      $post = $this->postModel->getSingleById($id);

      $this->data['title'] = $post->title;
      $this->data['body'] = MarkdownToHTML($post->body);
      $this->data['id'] = $id;
      $this->data['titleError'] = NULL;
      $this->data['bodyError'] = NULL;

      // Check if the post exists
      // Check if the user has to right to overrule this post
      // Or check if this person has made this post and is (still) an admin
      $user = &$this->data["user"];
      if(empty($post) ||
          (!in_array("OverrulePosts", $user->adminRights) &&
          ($user->id != $post->userId || !in_array("AddPosts", $user->adminRights))))
      {
        redirect('posts');
      }

      if($_SERVER['REQUEST_METHOD'] == 'POST')
      {

        // Sanitize POST array
        // First we Convert this stuff to Markdown
        $_POST['body'] = HTMLToMarkdown($_POST['body']);
        // Now filter out the remainder!
        // Note that we use filter_var_array here!
        $_POST = filter_var_array($_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $this->data['title'] = trim($_POST['title']);
        $this->data['body'] = trim($_POST['body']);

        // Validate the title
        if(empty($this->data['title']))
        {
          $this->data['titleError'] = 'Please enter title';
        }
        else
        {
          $this->data['titleError'] = false;
        }
        // Validate the body
        if(empty($this->data['body']))
        {
          $this->data['bodyError'] = 'Please enter body text';
        }
        else
        {
          $this->data['bodyError'] = false;
        }

        // make sure no errors
        if(empty($this->data['titleError'])
          && empty($this->data['bodyError'])
        ) {
          // Validated
          $updateData = [
            'title' => $this->data['title'],
            'body' => $this->data['body']
          ];

          if($this->postModel->updateById($id, $updateData))
          {
            flash('post_message', 'Post updated!');
            redirect('posts');
          }
          else
          {
            die('Something went wrong');
          }
        }
        else
        {
          $this->data['body'] = MarkdownToHTML($this->data['body']);
        }
      }

      $this->view('posts/edit', $this->data);
    }

    /************************
    *
    *
    * DELETE
    * @PARAM: post id
    *
    *
    *************************/
    public function delete(int $id){
      // Get existing post
      $post = $this->postModel->getSingleById($id);

      // Check if the post exists
      // Check if the user has to right to overrule this post
      // Or check if this person has made this post and is (still) an admin
      $user = &$this->data['user'];
      if(empty($post) ||
          (!in_array("OverrulePosts", $user->adminRights) &&
          ($user->id != $post->userId || !in_array("AddPosts", $user->adminRights))))
      {
        redirect('posts');
      }

      if($this->postModel->deletebyId($id)){
        flash('post_message', 'Post Removed');
        redirect('posts');
      }else{
        die('Something went wrong!');
      }
    }
  }
