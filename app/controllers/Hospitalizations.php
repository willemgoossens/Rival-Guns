<?php
    namespace App\Controllers;
    use App\Libraries\Controller as Controller;
    
    class Hospitalizations extends Controller
    {
        /**
         * 
         * 
         * Hospitalized
         * @return Void
         * 
         * 
         */
        public function hospitalized (): Void
        {
            $user = &$this->data['user'];

            $hospitalization = $this->hospitalizationModel->getSingleByUserId( $user->id );

            $endDate = $hospitalization->hospitalizedUntil;

            $now = new \DateTime;

            $this->data['interval'] =  $endDate->getTimestamp() - $now->getTimestamp();

            $this->view( 'hospitalizations/hospitalized', $this->data );
        }

    }