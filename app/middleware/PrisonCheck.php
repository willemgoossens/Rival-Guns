<?php
    namespace App\Middleware;
    use App\Libraries\Middleware as Middleware;

    class PrisonCheck extends Middleware
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
            $imprisonment = $this->imprisonmentModel->getSingleByUserId($_SESSION["userId"]);
            if( empty($imprisonment) )
            {
                return true;
            }

            $releaseDate = new \DateTime($imprisonment->imprisonedUntil);

            $now = new \DateTime();

            if( $releaseDate > $now )
            {
                // In prison
                if( 
                    $this->controller != "prisons"
                    || ($this->controller == "prisons" && $this->method == "index")
                ) {
                    redirect('prison/inside');
                }
            }
            else
            {
                $this->imprisonmentModel->deleteById($imprisonment->id);

                if(
                    $this->controller == "prisons" 
                    && $this->method != "index"
                ) {
                    redirect('prison/index');
                }
            }

            return true;
        }
    }
