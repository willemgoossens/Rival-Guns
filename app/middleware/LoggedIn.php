<?php
    namespace App\Middleware;
    use App\Libraries\Middleware as Middleware;

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
            if( $this->userModel->isLoggedIn() )
            {
                return true;
            }
            else
            { die("test");
                header("HTTP/1.1 401 Unauthorized");
                redirect('users/login');
            }
        } 
    }
