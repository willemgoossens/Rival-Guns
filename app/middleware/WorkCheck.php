<?php
    namespace App\Middleware;
    use App\Libraries\Middleware as Middleware;

    class WorkCheck extends Middleware
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
            $job = $this->jobModel->getSingleByUserId( $_SESSION['userId'], 'id', 'workingUntil' );
            
            $now = new \DateTime();
            
            if( strtotime( $job->workingUntil ) <= $now->getTimestamp() ) 
            {
                $this->jobModel->finishJobById( $job->id );
            }

            return true;
        }
    }
