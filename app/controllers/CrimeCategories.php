<?php
    namespace App\Controllers;
    use App\Libraries\Controller as Controller;
    
    class crimeCategories extends Controller
    {
        /**
         * 
         * 
         * Index
         * @param String type
         * @return Void
         * 
         * 
         */
        public function index( String $type ): Void
        {
            $user = &$this->data['user'];
            
            if( $type == "crimes" )
            {
                $dbType = "Crimes";
            }
            elseif( $type == "mafiajobs" )
            {
                $dbType = "Mafia Jobs";
            }
            else 
            {
                redirect( 'profile' );
            }
            $crimeCategories = $this->crimeCategoryModel->getByMainCategory( $dbType );

            if( $user->health < 5 )
            {
                $this->data['lowHealthWarning'] = true;
            }
            if( $user->energy < 5 )
            {
                $this->data['lowEnergyWarning'] = true;
            }

            $this->data['crimeCategories'] = $crimeCategories;
            $this->data['title'] = ucfirst( $dbType );

            $this->view( 'crimeCategories/index', $this->data );
        }
    }
