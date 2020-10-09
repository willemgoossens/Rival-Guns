<?php
    namespace App\Controllers;
    use App\Libraries\Controller as Controller;
    
    class Properties extends Controller
    {        
        /**
         * 
         * 
         * Index
         * @param Int $page
         * @return Void
         * 
         * 
         */
        public function index ( Int $page = 1 ): Void
        {
            $user = &$this->data['user'];

            $user->amountOfProperties = $this->propertyModel->countByUserId( $user->id );

            $limit = 15;
            if( ( $page - 1 ) * $limit > $user->amountOfProperties )
            {
                $page = 1;
            }
            $offset = ( $page - 1 ) * $limit;
            
            $user->properties = $this->propertyModel->offset( $offset )
                                                    ->limit( $limit )
                                                    ->getByUserId( $user->id );
            $propertyCategoryIds = array_column( $user->properties, 'propertyCategoryId' );

            $propertyCategories = $this->propertyCategoryModel->getFlaggedUniqueById( $propertyCategoryIds );

            $this->data['propertyCategories'] = $propertyCategories;
            $this->data['page'] = $page;
            $this->data['propertiesPerPage'] = $limit;

            $this->view('properties/index', $this->data);
        }


        /**
         * 
         * 
         * Show
         * @param Int propertyId
         * @return Void
         * 
         * 
         */
        public function show( Int $propertyId ): Void
        {
            $user = &$this->data['user'];
            $property = $this->propertyModel->getSingleByIdAndUserId( $propertyId, $user->id );

            if( ! $property )
            {
                redirect('profile');
            }
         
            // If we're already installing something
            // Set $now last. In case the installingUntil is NULL, it will generate the current timestamp. But $now has to be higher or equal
            $currentlyInstallingUntil = new \DateTime($property->installingUntil);
            $now = new \DateTime;
            $property->underConstruction = $currentlyInstallingUntil > $now ? true : false;
            $property->underConstructionSeconds = $currentlyInstallingUntil->getTimestamp() - $now->getTimestamp();

            $this->data['property'] = $property;
            $this->data['propertyCategory'] = $this->propertyCategoryModel->getSingleById( $property->propertyCategoryId );
            $this->data['businessCategory'] = isset( $property->businessCategoryId ) ? $this->businessCategoryModel->getSingleById( $property->businessCategoryId ) : null ;

            $this->view('properties/show', $this->data);
        }


        /**
         * 
         * 
         * Change
         * @param Int propertyId
         * @return Void
         * 
         * 
         */
        public function change ( Int $propertyId ): Void
        {
            $user = &$this->data['user'];
            $property = $this->propertyModel->getSingleByIdAndUserId( $propertyId, $user->id );

            if( ! $property )
            {
                redirect( 'profile' );
            }
         
            // If we're already installing something
            // Set $now last. In case the installingUntil is NULL, it will generate the current timestamp. But $now has to be higher or equal
            $currentlyInstallingUntil = new \DateTime($property->installingUntil);
            $now = new \DateTime;

            if( $currentlyInstallingUntil > $now )
            {
                redirect( 'profile' );
            }
            
            $businessCategoryIds = $this->propertyCategoryModel->getBusinessCategoryIdsForId( $property->propertyCategoryId );
            $businessCategories = $this->businessCategoryModel->getFlaggedUniqueById( $businessCategoryIds );

            if( $_SERVER['REQUEST_METHOD'] == 'POST' )
            {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $businessCategoryId = (int) $_POST['businessCategoryId'];

                if( 
                    ! in_array($businessCategoryId, $businessCategoryIds)
                    || $businessCategoryId == $property->businessCategoryId
                ) {
                    redirect( 'profile' );
                }

                $businessCategory = $businessCategories[$businessCategoryId];

                if( 
                    ( ! $businessCategory->isLegal && $businessCategory->installationCosts > $user->cash )
                    || ( $businessCategory->isLegal && $businessCategory->installationCosts > $user->bank )
                ) {
                    flash('propertyChange', 'You don\'t have enough money for the installation of this business.', 'alert alert-danger');
                }
                else
                {
                    $installingUntil = new \DateTime;
                    $installingUntil->add(new \DateInterval('PT' . $businessCategory->installationTime . 'S'));

                    $updateArray = [
                        'businessCategoryId' => $businessCategoryId,
                        'totalProfit' => 0,
                        'installingUntil' => $installingUntil->format('Y-m-d H:i:s')
                    ];
                    $this->propertyModel->updateById($propertyId, $updateArray);
    
                    flash('propertyChange', 'You have updated the function of property #' . $propertyId . '.');
                    redirect( 'properties/' . $propertyId );
                }                
            }

            $this->data['property'] = $property;
            $this->data['propertyCategory'] = $this->propertyCategoryModel->getSingleById( $property->propertyCategoryId );
            $this->data['businessCategories'] = $businessCategories;
            
            $this->view('properties/change', $this->data);
        }
    }