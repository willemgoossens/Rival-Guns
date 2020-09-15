<?php
    namespace App\Controllers;
    use App\Libraries\Controller as Controller;
    
    class Users extends Controller 
    {
        /**
         * 
         * 
         * Register
         * @return Void
         * 
         * 
         */
        public function register (): void
        {
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


            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                $this->data['name']            = $_POST['name'] ?? '';
                $this->data['email']           = $_POST['email'] ?? '';
                $this->data['password']        = $_POST['password'] ?? '';
                $this->data['confirmPassword'] = $_POST['confirmPassword'] ?? '';


                if(empty($this->data['email']))
                {
                    $this->data['emailError'] = 'Please enter email';
                }
                elseif(! filter_var($this->data['email'], FILTER_VALIDATE_EMAIL))
                {
                    $this->data['emailError'] = 'Please enter a valid email';
                }
                else
                {
                    if($this->userModel->existsByEmail($this->data['email']))
                    {
                        $this->data['emailError'] = 'Email is already taken';
                    }
                    else
                    {
                        $this->data['emailError'] = false;
                    }
                }

                
                if( empty($this->data['name']))
                {
                    $this->data['nameError'] = 'Please enter name';
                }
                elseif(! preg_match('/^[a-zA-Z0-9-_]+$/', $this->data['name']))
                {
                    $this->data['nameError'] = 'Your username can only contain alphanumeric values, - and _.';
                }
                elseif( strlen($this->data['name']) > 16)
                {
                    $this->data['nameError'] = 'Your name can be at most 16 characters long.';
                }
                elseif( strlen($this->data['name']) < 6)
                {
                    $this->data['nameError'] = 'Your name must be at least 6 characters long.';
                }
                else
                {
                    if($this->userModel->existsByName($this->data['name']))
                    {
                        $this->data['nameError'] = 'This name has already been taken';
                    }
                    else
                    {
                        $this->data['nameError'] = false;
                    }
                }

                
                if( empty($this->data['password']))
                {
                    $this->data['passwordError'] = 'Please enter password';
                }
                elseif( strlen($this->data['password']) < 6)
                {
                    $this->data['passwordError'] = 'Password must be at least 6 characters.';
                }
                elseif( strlen($this->data['password']) > 32)
                {
                    $this->data['passwordError'] = 'Password can be at most 32 characters long.';
                }
                else
                {
                    $this->data['passwordError'] = false;
                }

                
                if( empty($this->data['confirmPassword']))
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


                if(
                    empty($this->data['emailError'])
                    && empty($this->data['nameError'])
                    && empty($this->data['passwordError'])
                    && empty($this->data['confirmPasswordError'])
                ) {
                    $this->data['password'] = password_hash($this->data['password'], PASSWORD_DEFAULT);

                    $insertData = [
                        'name' => $this->data['name'],
                        'email' => trim($this->data['email']),
                        'password' => $this->data['password'],
                    ];
                    
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


        /**
         * 
         * 
         * Login
         * @return Void
         * 
         * 
         */
        public function login (): Void
        {
            $this->data =[
                'email' => '',
                'password' => '',
                'emailError' => NULL,
                'passwordError' => NULL
            ];
            
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                $this->data['email'] = $_POST['email'] ?? '';
                $this->data['password'] = $_POST['password'] ?? '';
                
                if(empty($this->data['email']))
                {
                    $this->data['emailError'] = 'Please enter email';
                }
                elseif(! $this->userModel->existsByEmail($this->data['email']))
                {
                    $this->data['emailError'] = 'No user found';
                }
                else
                {
                    $this->data['emailError'] = false;
                }

                
                if( empty($this->data['password']))
                {
                    $this->data['passwordError'] = 'Please enter password';
                }
                else
                {
                    $this->data['passwordError'] = false;
                }

                
                if(
                    empty($this->data['emailError']) 
                    && empty($this->data['passwordError'])
                ) {
                    $loggedInUser = $this->userModel->login($this->data['email'], $this->data['password']);

                    if($loggedInUser)
                    {
                        $this->userModel->createUserSession($loggedInUser);
                    } 
                    else 
                    {
                        $this->data['passwordError'] = 'Password incorrect';
                    }
                }
            }

            // Load view
            $this->view('users/login', $this->data);
        }

        /**
         * 
         * 
         * Logout
         * @return Void
         * 
         * 
         */
        public function logout (): void
        {
            unset($_SESSION['userId']);
            session_destroy();
            redirect('users/login');
        }

        /**
         * 
         * 
         * ForgottenPassword
         * @param Int userId
         * @param String resetKey
         * @return Void
         * 
         * 
         */
        public function forgottenPassword(int $userId = null, string $resetKey = null): void
        {
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
                if(
                    ! isset($userId) 
                    || ! isset($resetKey)
                ) {
                    $this->data['email'] = $_POST['email'] ?? '';

                    if(! filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)){
                        $this->data['emailError'] = 'Please enter a valid email';
                    }
                    else
                    {
                        $user = $this->userModel->getSingleByEmail($this->data['email'], 'id');

                        if (! $user) 
                        {
                            $this->data['emailError'] = 'Please enter a valid email';
                        }
                        else 
                        {
                            // Generate a random key, a random integer that is encrypted
                            // + a random salt to prevent people from bruteforcing the key
                            $resetKey = md5(rand(1000000, 99999999) . ENCRYPT_SALT);
                            
                            $updateArray = [
                              'resetKey' => $resetKey
                            ];
                            if($this->userModel->updateById($user->id, $updateArray))
                            {
                                flash('forgetPassword', 'An e-mail has been send to: ' . $this->data['email']);
                            }
                            else
                            {
                                flash('forgetPassword', 'Something went wrong! Try again...', 'alert alert-error');
                            }
                        }
                    }
                }
                else
                {
                    $user = $this->userModel->getSingleById($userId);
                    
                    if(
                        $user 
                        && $user->resetKey == $resetKey
                    ) {
                        $this->data['password'] = $_POST['password'] ?? '';
                        $this->data['confirmPassword'] = $_POST['confirmPassword'] ?? '';
                        

                        if( empty($this->data['password']))
                        {
                            $this->data['passwordError'] = 'Please enter password';
                        }
                        elseif( strlen($this->data['password']) < 6)
                        {
                            $this->data['passwordError'] = 'Password must be at least 6 characters';
                        }
                        else
                        {
                            $this->data['passwordError'] = false;
                        }


                        if( empty($this->data['confirmPassword']))
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

                        if(
                            empty($this->data['passwordError'])
                            && empty($this->data['confirmPasswordError'])
                        ) {
                            $updateArray = [
                                'password' => password_hash($this->data['password'], PASSWORD_DEFAULT),
                                'resetKey' => ''
                            ];
                            if($this->userModel->updateById($userId, $updateArray))
                            {
                                flash('forgot_password_success', 'Your password has been changed!');
                                redirect('users/login');
                            }
                            else
                            {
                                flash('forgetPassword', 'Something went wrong! Try again...', 'alert alert-error');
                            }
                        }
                    }
                }
            }


            if(
                !empty($userId) 
                && !empty($resetKey)
            ) {
                $user = $this->userModel->getSingleById($userId);
                if($user && $user->resetKey == $resetKey)
                {
                    $this->data['resetPw']  = true;
                    $this->data['userId']   = $userId;
                    $this->data['resetKey'] = $resetKey;
                }
            }

            $this->view('users/forgottenPassword', $this->data);
        }

        
        /**
         * 
         * 
         * editProfile
         * @return Void
         * 
         * 
         */
        public function editProfile (): void
        {
            $user = &$this->data['user'];

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

            if($_SERVER['REQUEST_METHOD'] == "POST")
            {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                if(isset($_POST['changeProfile']))
                {
                    $this->data['email'] = $_POST['email'];
                    $this->data['password'] = $_POST['password'];

                    if(!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL))
                    {
                        $this->data['emailError'] = 'Please enter a valid email';
                    }
                    else
                    {
                        $this->data['emailError'] = false;
                    }

                    $hashedPassword = $user->password;
                    if( !password_verify($this->data['password'], $hashedPassword))
                    {
                        $this->data['passwordError'] = 'This is not your valid password.';
                    }
                    else
                    {
                        $this->data['passwordError'] = false;
                    }

                    if( 
                        empty($this->data['emailError'])
                        && empty($this->data['passwordError'])
                    ) {
                        $updateArray = [
                            'email' => $this->data['email']
                        ];
                        $this->userModel->updateById($user->id, $updateArray);

                        $this->data['password'] = '';
                        flash('edit_success', 'Your profile has been edited successfully!');
                    }
                }
                else
                {
                    $this->data['newPassword'] = $_POST['newPassword'];
                    $this->data['repeatNewPassword'] = $_POST['repeatNewPassword'];
                    $this->data['password2'] = $_POST['password2'];
                    
                    if( empty($this->data['newPassword']))
                    {
                        $this->data['newPasswordError'] = 'Please enter password';
                    }
                    elseif( strlen($this->data['newPassword']) < 6)
                    {
                        $this->data['newPasswordError'] = 'Password must be at least 6 characters';
                    }
                    else
                    {
                        $this->data['newPasswordError'] = false;
                    }

                    if( empty($this->data['repeatNewPassword']))
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

                    $hashed_password = $user->password;
                    if(! password_verify($this->data['password2'], $hashed_password))
                    {
                        $this->data['password2Error'] = 'This is not your valid password.';
                    }
                    else
                    {
                        $this->data['password2Error'] = false;
                    }
                    
                    // If the errors are empty, we can change the profile details
                    if( 
                        empty($this->data['password2Error'])
                        && empty($this->data['newPasswordError'])
                        && empty($this->data['repeatNewPasswordError'])
                    ) {
                        $updateArray = [
                            'password' => password_hash($this->data['newPassword'], PASSWORD_DEFAULT)
                        ];
                        $this->userModel->updateById($user->id, $updateArray);

                        $this->data['newPassword'] = '';
                        $this->data['repeatNewPassword'] = '';
                        $this->data['password2'] = '';

                        flash('edit_success', 'Your password has been changed successfully!');
                    }
                }
            }

            $this->view('users/editProfile', $this->data);
        }

        
        /**
         * 
         * 
         * myProfile
         * @return Void
         * 
         * 
         */
        public function myProfile (): Void
        {
            $user = &$this->data['user'];

            $user->wearables = $this->wearableModel->getByUserId( $user->id) ;
            $wearableCategories = array_column( $user->wearables, 'wearableCategoryId' );
            $this->data['wearableCategories'] = $this->wearableCategoryModel->getFlaggedUniqueById( $wearableCategories );

            $user->stars = $this->criminalRecordModel->calculateStars( $user->id );
            $user->criminalRecord = $this->criminalRecordModel->getByUserId( $user->id );

            $this->data['posts'] = $this->postModel->orderBy( "createdAt", "DESC" )->limit( 3 )->get();

            $this->view( 'users/myProfile', $this->data );
        }
    }
