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
                if( 
                    $this->controller == "prisons" 
                    && $this->method != "index"
                ) {
                    redirect('');
                }
                return true;
            }

            $releaseDate = $imprisonment->createdAt;
            $sentences = $this->sentenceModel->getByUserId( $_SESSION['userId'] );
            $totalTime = array_sum( array_column( $sentences, "timeRemaining" ) );
            $releaseDate->modify( '+' . $totalTime . ' second' );

            $now = new \DateTime;

            if( $releaseDate > $now )
            {
                // In prison
                if( 
                    $this->controller != "prisons"
                    || ( $this->controller == "prisons" 
                         && $this->method == "index" )
                ) {
                    redirect('prison/inside');
                }
            }

            return true;
        }
    }
