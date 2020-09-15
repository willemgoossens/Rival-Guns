<?php
    namespace App\Controllers;
    use App\Libraries\Controller as Controller;
    
    class Posts extends Controller 
    {
        /**
         * 
         * 
         * Index
         * @param Int page
         * @return Void
         * 
         * 
         */
        public function index ( Int $page = 1 ): Void
        {
            $postsCount = $this->postModel->count();
            $limit = 5;

            if(
                $page < 1
                || $page * $limit - $postsCount > $limit
            ) {
                $page = 1;
            }
            // Now we calculate the first and
            $offset = ( $page - 1 ) * $limit;

            // Get Posts
            $posts = $this->postModel->orderBy( "createdAt", "DESC" )
                                    ->limit( $limit )
                                    ->offset( $offset )
                                    ->get();

            $this->data['posts'] = $posts;
            $this->data['page'] = $page;
            $this->data['casesPerPage'] = $limit;
            $this->data['totalCases'] = $postsCount;
            $this->data['canAdd'] = in_array( "AddPosts", $this->data['user']->adminRights ) ? true : false;

            $this->view( 'posts/index', $this->data );
        }

        /**
        *
        *
        * Add
        * @return Void
        *
        *
        */
        public function add (): void
        {
            // First check if the user has the rights to add posts
            $user = &$this->data["user"];

            if( ! in_array( "AddPosts", $this->data["user"]->adminRights ) )
            {
                redirect( 'posts' );
            }

            $this->data['title'] = '';
            $this->data['body'] = '';
            $this->data['titleError'] = NULL;
            $this->data['bodyError'] = NULL;

            if( $_SERVER['REQUEST_METHOD'] == 'POST' )
            {
                $_POST['body'] = HTMLToMarkdown( $_POST['body'] );
                $_POST = filter_var_array( $_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

                $this->data['title'] = trim( $_POST['title'] );
                $this->data['body'] = trim( $_POST['body'] );

                if( empty($this->data['title']) )
                {
                    $this->data['titleError'] = 'Please enter title';
                }
                else
                {
                    $this->data['titleError'] = false;
                }
                
                if( empty($this->data['body']) )
                {
                    $this->data['bodyError'] = 'Please enter body text';
                }
                else
                {
                    $this->data['bodyError'] = false;
                }


                if(
                    empty( $this->data['titleError'] )
                    && empty( $this->data['bodyError'] )
                ) {
                    $insertData = [
                        'title' => $this->data['title'],
                        'body' => $this->data['body'],
                        'userId' => $user->id
                    ];

                    if($this->postModel->insert($insertData))
                    {
                        flash( 'post_message', 'Your post has been added!' );
                        redirect( 'posts' );
                    }
                    else
                    {
                        die( 'Something went wrong' );
                    }
                }
                else
                {
                    $this->data['body'] = MarkdownToHTML( $this->data['body'] );
                }
            }

            $this->view('posts/add', $this->data);
        }

        /**
        *
        *
        * Show
        * @param Int postId
        * @return Void
        *
        *
        */
        public function show (int $postId): void
        {
            $user = &$this->data['user'];

            $post = $this->postModel->getSingleById($postId);
            if(empty($post))
            {
                redirect('posts');
            }
            else
            {
                $poster = $this->userModel->getSingleById($post->userId);
                $post->body = MarkdownToHTML($post->body);

                if(
                    in_array('OverrulePosts', $user->adminRights) ||
                    (   $_SESSION['userId'] == $poster->id 
                        && in_array('AddPosts', $user->adminRights)
                    )
                ) {
                    $canEdit = true;
                }
                else
                {
                    $canEdit = false;
                }

                $this->data['post'] = $post;
                $this->data['poster'] = $poster;
                $this->data['canEdit'] = $canEdit;

                $this->view('posts/show', $this->data);
            }
        }

        /**
         * 
         * 
         * Edit
         * @param Int postId
         * @return Void
         * 
         * 
         */
        public function edit (Int $postId): Void
        {
            $user = &$this->data["user"];

            $post = $this->postModel->getSingleById($postId);
            if(
                  empty($post) 
                  || (! in_array("OverrulePosts", $user->adminRights) 
                  && (
                          $user->id != $post->userId 
                          || !in_array("AddPosts", $user->adminRights)
                      ) )
            ) {
                redirect('posts');
            }

            $this->data['title'] = $post->title;
            $this->data['body'] = MarkdownToHTML($post->body);
            $this->data['id'] = $postId;
            $this->data['titleError'] = NULL;
            $this->data['bodyError'] = NULL;


            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $_POST['body'] = HTMLToMarkdown($_POST['body']);
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

                if(empty($this->data['body']))
                {
                    $this->data['bodyError'] = 'Please enter body text';
                }
                else
                {
                    $this->data['bodyError'] = false;
                }

                if(
                    empty($this->data['titleError'])
                    && empty($this->data['bodyError'])
                ) {
                    $updateData = [
                        'title' => $this->data['title'],
                        'body' => $this->data['body']
                    ];

                    if($this->postModel->updateById($postId, $updateData))
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

        /**
         * 
         * 
         * Delete
         * @param Int postId
         * @return Void
         * 
         * 
         */
        public function delete (int $postId): void
        {
            $user = &$this->data['user'];

            $post = $this->postModel->getSingleById($postId);
            if(
                  empty($post) 
                  || ( 
                          !in_array("OverrulePosts", $user->adminRights) 
                          && (
                              $user->id != $post->userId 
                              || !in_array("AddPosts", $user->adminRights)
                          )
                      )
            ) {
                redirect('posts');
            }

            if($this->postModel->deletebyId($postId))
            {
                flash('post_message', 'Post Removed');
                redirect('posts');
            }
            else
            {
                die('Something went wrong!');
            }
        }
    }
