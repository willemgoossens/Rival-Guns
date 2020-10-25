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
            $hospitalization = $this->hospitalizationModel->getSingleByUserId($_SESSION["userId"]);
            if( empty($hospitalization) )
            {
                if( $this->controller == "hospitalizations" ) 
                {
                    redirect('');
                }

                return true;
            }

            $releaseDate = new \DateTime( $hospitalization->hospitalizedUntil );

            $now = new \DateTime();

            if( $releaseDate > $now )
            {
                if( 
                    $this->controller != "hospitalizations"
                    || ($this->controller != "hospitalizations" && $this->method != "hospitalized")
                ) {
                    redirect('hospitalized');
                }
            }

            return true;
        }
    }
