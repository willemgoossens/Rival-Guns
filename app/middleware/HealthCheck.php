<?php
    namespace App\Middleware;
    use App\Libraries\Middleware as Middleware;

    class HealthCheck extends Middleware
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
            // Add the pages

            //$this->hospitalizationModel->calculateHealthAndEnergyForUser($_SESSION['userId'], new \DateTime);

            return true;
        }
    }
