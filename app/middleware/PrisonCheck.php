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

            $criminalRecords = $this->criminalRecordModel->getByImprisonmentId($imprisonment->id);

            $crimeTypeIds = array_column($criminalRecords, "type");
            $crimeTypes = $this->crimeTypeModel
                               ->getFlaggedUniqueById($crimeTypeIds);

            $interval = 0;
            foreach( $criminalRecords as $record )
            {                
                $interval += $crimeTypes[$record->type]->jailTime;
            }
            $interval = new \DateInterval('PT' . $interval . 'S');            
            $releaseDate = new \DateTime($imprisonment->createdAt);
            $releaseDate->add($interval);

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
