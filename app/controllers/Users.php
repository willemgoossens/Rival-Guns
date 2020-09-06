<?php

    class Users extends Controller 
    {

        public function __construct()
        {
            $this->userModel = $this->model('User');
            $this->conversationModel = $this->model('Conversation');
            $this->adminRoleModel = $this->model('AdminRole');
            $this->wearableModel = $this->model('Wearable');
            $this->wearableCategoryModel = $this->model('WearableCategory');
            $this->criminalRecordModel = $this->model('CriminalRecord');
            $this->postModel = $this->model('Post');

            if($this->userModel->isLoggedIn())
            {
                // Set the sessions for the nav bar
                $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
                $this->data['user']->adminRights         = $this->adminRoleModel->getRightNamesForRole($this->data['user']->adminRole);
                $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
            }
        }

        /***************************
        *
        *
        * Register
        *
        *
        ****************************/
        public function register() {

          // Init data
          $this->data = [
            'name' => '',
            'email' => '',
            'password' => '',
            'confirmPassword' => '',
            'nameError' => NULL,
            'emailError' => NULL,
            'passwordError' => NULL,
            'confirmPasswordError' => NULL
          ];

          // Check for POST
          if($_SERVER['REQUEST_METHOD'] == 'POST')
          {
            // Process form

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $this->data['name']            = $_POST['name'] ?? '';
            $this->data['email']           = $_POST['email'] ?? '';
            $this->data['password']        = $_POST['password'] ?? '';
            $this->data['confirmPassword'] = $_POST['confirmPassword'] ?? '';

            // Validate Email
            if(empty($this->data['email']))
            {
              $this->data['emailError'] = 'Please enter email';
            }
            else if(!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL))
            {
              $this->data['emailError'] = 'Please enter a valid email';
            }
            else
            {
              // Check email
              if($this->userModel->existsByEmail($this->data['email']))
              {
                $this->data['emailError'] = 'Email is already taken';
              }
              else
              {
                $this->data['emailError'] = false;
              }
            }

            // Validate Name
            if(empty($this->data['name']))
            {
              $this->data['nameError'] = 'Please enter name';
            }
            elseif(!preg_match('/^[a-zA-Z0-9-_]+$/', $this->data['name']))
            {
              $this->data['nameError'] = 'Your username can only contain alphanumeric values, - and _.';
            }
            elseif(strlen($this->data['name']) > 16)
            {
              $this->data['nameError'] = 'Your name can be at most 16 characters long.';
            }
            elseif(strlen($this->data['name']) < 6)
            {
              $this->data['nameError'] = 'Your name must be at least 6 characters long.';
            }
            else
            {
              // Check if the username already is taken
              if($this->userModel->existsByName($this->data['name']))
              {
                $this->data['nameError'] = 'This name has already been taken';
              }
              else
              {
                $this->data['nameError'] = false;
              }
            }

            // Validate Password
            if(empty($this->data['password']))
            {
              $this->data['passwordError'] = 'Please enter password';
            }
            elseif(strlen($this->data['password']) < 6)
            {
              $this->data['passwordError'] = 'Password must be at least 6 characters.';
            }
            elseif(strlen($this->data['password']) > 32)
            {
              $this->data['passwordError'] = 'Password can be at most 32 characters long.';
            }
            else
            {
              $this->data['passwordError'] = false;
            }

            // Validate Confirm Password
            if(empty($this->data['confirmPassword']))
            {
              $this->data['confirmPasswordError'] = 'Please confirm password';
            }
            elseif($this->data['password'] != $this->data['confirmPassword'])
            {
              $this->data['confirmPasswordError'] = 'Passwords do not match';
            }
            else
            {
              $this->data['confirmPasswordError'] = false;
            }

            // Make sure errors are empty
            if(empty($this->data['emailError'])
              && empty($this->data['nameError'])
              && empty($this->data['passwordError'])
              && empty($this->data['confirmPasswordError'])
            ) {
              // Validated

              // Hash Password
              $this->data['password'] = password_hash($this->data['password'], PASSWORD_DEFAULT);

              $insertData = [
                'name' => $this->data['name'],
                'email' => trim($this->data['email']),
                'password' => $this->data['password'],
              ];
              // Register User
              if($this->userModel->insert($insertData))
              {
                flash('register_success', 'You are registered and can log in');
                redirect('users/login');
              }
              else
              {
                die('Something went wrong');
              }
            }
          }

          // Load view
          $this->view('users/register', $this->data);
        }

        public function login() {

          // Init data
          $this->data =[
            'email' => '',
            'password' => '',
            'emailError' => NULL,
            'passwordError' => NULL
          ];
          // Check for POST
          if($_SERVER['REQUEST_METHOD'] == 'POST')
          {
            // Process form
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $this->data['email'] = $_POST['email'] ?? '';
            $this->data['password'] = $_POST['password'] ?? '';
            // Validate Email
            if(empty($this->data['email']))
            {
              $this->data['emailError'] = 'Please enter email';
            }
            elseif(!$this->userModel->existsByEmail($this->data['email']))
            {
              $this->data['emailError'] = 'No user found';
            }
            else
            {
              $this->data['emailError'] = false;
            }

            // Validate Password
            if(empty($this->data['password']))
            {
              $this->data['passwordError'] = 'Please enter password';
            }
            else
            {
              $this->data['passwordError'] = false;
            }

            // Make sure errors are empty
            if(empty($this->data['emailError']) && empty($this->data['passwordError']))
            {
              // Validated
              // Check and set logged in user
              $loggedInUser = $this->userModel->login($this->data['email'], $this->data['password']);

              if($loggedInUser){
                // Create Session
                $this->createUserSession($loggedInUser);
              } else {
                $this->data['passwordError'] = 'Password incorrect';
              }
            }
          }

          // Load view
          $this->view('users/login', $this->data);
        }

        public function createUserSession($user){
          $_SESSION['userId'] = $user->id;
          redirect('');
        }

        public function logout(){
          unset($_SESSION['userId']);
          session_destroy();
          redirect('users/login');
        }

        /***************************
        *
        *
        * Forgotten Password
        *
        *
        ****************************/
        public function forgottenPassword(int $id = null, string $resetKey = null){
          $this->data = [
            'email' => '',
            'emailError' => NULL,
            'resetPw' => false,
            'password' => '',
            'passwordError' => NULL,
            'confirmPassword' => '',
            'confirmPasswordError' => NULL
          ];
          // If a post request is made
          if($_SERVER['REQUEST_METHOD'] == 'POST')
          {
            // Process form
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            // if variables were passed in the url
            if(! isset($id) || ! isset($resetKey))
            {
              $this->data['email'] = $_POST['email'] ?? '';

              if(!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)){
                $this->data['emailError'] = 'Please enter a valid email';
              }
              // Check if the e-mail is not in use
              else
              {
                // First get the user
                $user = $this->userModel->getSingleByEmail($this->data['email'], 'id');

                if (!$user) {
                  $this->data['emailError'] = 'Please enter a valid email';
                }
                // Create a new reset code
                else {
                  // Generate a random key, a random integer that is encrypted
                  // + a random salt to prevent people from bruteforcing the key
                  $resetKey = md5(rand(1000000, 99999999) . ENCRYPT_SALT);
                  // Set the update array
                  $updateArray = [
                    'resetKey' => $resetKey
                  ];
                  // Now update the user
                  if($this->userModel->updateById($user->id, $updateArray)){
                    flash('forgetPassword', 'An e-mail has been send to: ' . $this->data['email']);
                  }else{
                    flash('forgetPassword', 'Something went wrong! Try again...', 'alert alert-error');
                  }
                }
              }
            }
            //
            // otherwise we assume they want to change their passport
            else
            {
              // Now get the user
              $user = $this->userModel->getSingleById($id);
              // Check if the user exists && the resetKey is similar to the user
              if($user && $user->resetKey == $resetKey)
              {
                $this->data['password'] = $_POST['password'] ?? '';
                $this->data['confirmPassword'] = $_POST['confirmPassword'] ?? '';
                // Validate Password
                if(empty($this->data['password']))
                {
                  $this->data['passwordError'] = 'Please enter password';
                }
                elseif(strlen($this->data['password']) < 6)
                {
                  $this->data['passwordError'] = 'Password must be at least 6 characters';
                }
                else
                {
                  $this->data['passwordError'] = false;
                }

                // Validate Confirm Password
                if(empty($this->data['confirmPassword']))
                {
                  $this->data['confirmPasswordError'] = 'Please confirm password';
                }
                elseif($this->data['password'] != $this->data['confirmPassword'])
                {
                  $this->data['confirmPasswordError'] = 'Passwords do not match';
                }
                else
                {
                  $this->data['confirmPasswordError'] = false;
                }

                // If there's no error
                if(empty($this->data['passwordError'])
                  && empty($this->data['confirmPasswordError'])
                ) {
                  // Set the update array
                  $updateArray = [
                    'password' => password_hash($this->data['password'], PASSWORD_DEFAULT),
                    'resetKey' => ''
                  ];
                  // Update the user
                  if($this->userModel->updateById($id, $updateArray))
                  {
                    // Now create a flash message
                    flash('forgot_password_success', 'Your password has been changed!');
                    // And redirect
                    redirect('users/login');
                  }
                  else
                  {
                    flash('forgetPassword', 'Something went wrong! Try again...', 'alert alert-error');
                  }
                }
              }
            }
          } // END OF THE POST METHOD

          // Check if user_id and the key have been set
          if(!empty($id) && !empty($resetKey))
          {
            // Now get the user
            $user = $this->userModel->getSingleById($id);
            // Check if the user exists && the resetKey is similar to the user
            if($user && $user->resetKey == $resetKey)
            {
              $this->data['resetPw']  = true;
              $this->data['userId']   = $id;
              $this->data['resetKey'] = $resetKey;
            }
          }

          $this->view('users/forgottenPassword', $this->data);
        }

        /***************************
        *
        *
        * Edit Profile
        *
        *
        ****************************/
        public function editProfile()
        {
            // Get the user's data
            $user = &$this->data['user'];


            // The data array
            $extraData = [
                'email' => $user->email,
                'emailError' => NULL,
                'newPassword' => '',
                'newPasswordError' => NULL,
                'repeatNewPassword' => '',
                'repeatNewPasswordError' => NULL,
                'password' => '',
                'passwordError' => NULL,
                'password2' => '',
                'password2Error' => NULL
            ];
            $this->data = array_merge($this->data, $extraData);

            // Check if the user made a post request
            if($_SERVER['REQUEST_METHOD'] == "POST")
            {
                // Filter the input
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                // If we're changing profile data
                if(isset($_POST['changeProfile']))
                {
                    // And add to the data array
                    $this->data['email'] = $_POST['email'];
                    $this->data['password'] = $_POST['password'];
                    // First validate the e-mailaddress
                    if(!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL))
                    {
                        $this->data['emailError'] = 'Please enter a valid email';
                    }
                    else
                    {
                        $this->data['emailError'] = false;
                    }
                    // Now check if the user actually filled in his password
                    $hashedPassword = $user->password;
                    if( !password_verify($this->data['password'], $hashedPassword))
                    {
                        $this->data['passwordError'] = 'This is not your valid password.';
                    }
                    else
                    {
                        $this->data['passwordError'] = false;
                    }

                    // If the errors are empty, we can change the profile details
                    if( empty($this->data['emailError'])
                        && empty($this->data['passwordError'])
                    ) 
                    {
                        // Create an input array for the database
                        $inputArray = [
                          'email' => $this->data['email']
                        ];
                        // Update the user
                        $this->userModel->updateById($user->id, $inputArray);
                        // And delete all the password stuff from the data array
                        $this->data['password'] = '';
                        // Flash message
                        flash('edit_success', 'Your profile has been edited successfully!');
                    }
                }
                // If the user tries to change his password
                else
                {
                    // And add to the data array
                    $this->data['newPassword'] = $_POST['newPassword'];
                    $this->data['repeatNewPassword'] = $_POST['repeatNewPassword'];
                    $this->data['password2'] = $_POST['password2'];
                    // Also validate the Passwords
                    if(empty($this->data['newPassword']))
                    {
                        $this->data['newPasswordError'] = 'Please enter password';
                    }
                    elseif(strlen($this->data['newPassword']) < 6)
                    {
                        $this->data['newPasswordError'] = 'Password must be at least 6 characters';
                    }
                    else
                    {
                        $this->data['newPasswordError'] = false;
                    }

                    // Validate Confirm Password
                    if(empty($this->data['repeatNewPassword']))
                    {
                        $this->data['repeatNewPasswordError'] = 'Please confirm password';
                    }
                    elseif($this->data['newPassword'] != $this->data['repeatNewPassword'])
                    {
                        $this->data['repeatNewPasswordError'] = 'Passwords do not match';
                    }
                    else
                    {
                        $this->data['repeatNewPasswordError'] = false;
                    }

                    // Now check if the user actually filled in his password
                    $hashed_password = $user->password;
                    if(!password_verify($this->data['password2'], $hashed_password))
                    {
                        $this->data['password2Error'] = 'This is not your valid password.';
                    }
                    else
                    {
                        $this->data['password2Error'] = false;
                    }
                    
                    // If the errors are empty, we can change the profile details
                    if( empty($this->data['password2Error'])
                        && empty($this->data['newPasswordError'])
                        && empty($this->data['repeatNewPasswordError'])
                    ) 
                    {
                        // Create an input array for the database
                        $inputArray = [
                          'password' => password_hash($this->data['newPassword'], PASSWORD_DEFAULT)
                        ];
                        // Update the user
                        $this->userModel->updateById($user->id, $inputArray);
                        // And delete all the password stuff from the data array
                        $this->data['newPassword'] = '';
                        $this->data['repeatNewPassword'] = '';
                        $this->data['password2'] = '';
                        // Flash message
                        flash('edit_success', 'Your password has been changed successfully!');
                    }
                }
            }

            $this->view('users/editProfile', $this->data);
        }

        /**
         * myProfile
         */
        public function myProfile ()
        {
            $user = &$this->data['user'];

            $user->wearables = $this->wearableModel->getByUserId($user->id);
            $wearableCategories = array_column($user->wearables, 'wearableCategoryId');
            $this->data['wearableCategories'] = $this->wearableCategoryModel->getFlaggedUniqueById($wearableCategories);

            $user->stars = $this->criminalRecordModel->calculateStars($user->id);
            $user->criminalRecord = $this->criminalRecordModel->getByUserId($user->id);

            $this->data['posts'] = $this->postModel->orderBy("createdAt", "DESC")->limit(3)->get();

            $this->view('users/myProfile', $this->data);
        }
    }
