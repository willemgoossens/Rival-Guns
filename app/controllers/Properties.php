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
            // Add future imprisonment
            // Rework the arrestation thing (sentences)
            // Add some explanatory text to this page
            $user = &$this->data['user'];
            $this->data['maxLaunderingAmount'] = $this->propertyModel->getMaxLaunderingAmountForUser( $user->id );
            
            $launderingLog = $this->launderingLogModel->getLaunderingLogOfTodayForUser( $user->id );
            if( $launderingLog )
            {
                $this->data['launderedAmount'] = $launderingLog->launderedAmount;
            }
            else 
            {
                $this->data['launderedAmount'] = 0;
            }

            if( $_SERVER['REQUEST_METHOD'] == 'POST' )
            {
                $_POST = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
                $this->data['amountToLaunder'] = (int) $_POST['amountToLaunder'];
                $this->data['amountToLaunderError'] = false;

                if( $this->data['maxLaunderingAmount'] <= 0 )
                {
                    redirect('');
                }
                elseif( $this->data['amountToLaunder'] > $user->cash )
                {
                    $this->data['amountToLaunderError'] = "You don't have enough cash money to launder.";
                }
                else
                {
                    if( $launderingLog )
                    {
                        if( $launderingLog->maxLaunderingAmount < $this->data['maxLaunderingAmount'] )
                        {
                            $launderingLog->maxLaunderingAmount = $this->data['maxLaunderingAmount'];
                        }

                        $launderingLog->launderedAmount += $this->data['amountToLaunder'];

                        $updateArray = [
                            'launderedAmount' => $launderingLog->launderedAmount,
                            'maxLaunderingAmount' => $launderingLog->maxLaunderingAmount
                        ];

                        $this->launderingLogModel->updateById( $launderingLog->id, $updateArray );
                    }
                    else
                    {
                        $insertArray = [
                            'userId' => $user->id,
                            'launderedAmount' => $this->data['amountToLaunder'],
                            'maxLaunderingAmount' => $this->data['maxLaunderingAmount']
                        ];

                        $this->launderingLogModel->insert($insertArray);
                    }

                    $user->bank += $this->data['amountToLaunder'];
                    $user->cash -= $this->data['amountToLaunder'];
                    $this->data['launderedAmount'] += $this->data['amountToLaunder'];

                    $updateArray = [
                        'bank' => $user->bank,
                        'cash' => $user->cash
                    ];
                    $this->userModel->updateById( $user->id, $updateArray );

                    flash('launderSuccess', 'You have successfully laundered &euro;' . $this->data['amountToLaunder'] . '!');

                    $crimeTypeIds = $this->crimeTypeModel->getIdsForLaunderingCrimes();
                    $futureImprisonment = $this->futureImprisonmentModel->getSingleByUserIdAndCrimeTypeId( $user->id, $crimeTypeIds );
                    if( $futureImprisonment )
                    {
                        $this->futureImprisonmentModel->deleteById( $futureImprisonment->id );
                    }

                    $this->futureImprisonmentModel->createFutureImprisonmentForLaunderingOrNotForUser( $user->id );
                }
            }

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
            $currentlyInstallingUntil = $property->installingUntil;
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
            $currentlyInstallingUntil = $property->installingUntil;
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