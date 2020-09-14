<?php
    namespace App\Middleware;
    use App\Libraries\Middleware as Middleware;

    class LoggedOut extends Middleware
    {
        public function __construct(string ...$setup)
        {
            $this->setVariables(...$setup);
        }

        /**
         * 
         * 
         * Before
         * @Return bool
         * 
         * 
         */
        public function before(): Bool
        {
            $this->userModel = $this->model('User');

            if( ! $this->userModel->isLoggedIn())
            {
                return true;
            }
            else
            {
                header("HTTP/1.1 401 Unauthorized");
                redirect('profile');
            }
        }
    }
