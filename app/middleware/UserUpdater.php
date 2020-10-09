<?php
    namespace App\Middleware;
    use App\Libraries\Middleware as Middleware;

    class UserUpdater extends Middleware
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
            $this->userModel->updateUserData( $_SESSION['userId'] );
            return true;
        }
    }
