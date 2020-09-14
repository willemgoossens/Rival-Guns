<?php

    class LoggedIn extends Middleware
    {
        public function __construct(string ...$setup)
        {
            $this->setVariables(...$setup);
        }


        /**
         * 
         * 
         * Before
         * @return Bool
         * 
         * 
         */
        public function before(): Bool
        {
            $this->userModel = $this->model('User');

            if( $this->userModel->isLoggedIn() )
            {
                return true;
            }
            else
            {
                header("HTTP/1.1 401 Unauthorized");
                redirect('users/login');
            }
        } 
    }
