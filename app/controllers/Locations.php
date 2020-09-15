<?php
    namespace App\Controllers;
    use App\Libraries\Controller as Controller;
    
    class Locations extends Controller
    {
        /**
         * 
         * 
         * Bank
         * @return Void
         * 
         * 
         */
        public function bank (): Void
        {
            $user = &$this->data['user'];

            if( $_SERVER['REQUEST_METHOD'] == 'POST' )
            {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $amount = (int) $_POST['amount'];

                $this->data['amount'] = $amount;
                $this->data['amountError'] = false;

                if(
                    empty($amount)
                    || $amount <= 0
                ) {
                    $this->data['amountError'] = 'This is not a valid amount.';
                    $this->data['amount'] = $amount;
                }
                elseif( isset($_POST['deposit']) )
                {
                    if( $user->cash < $amount )
                    {
                        $this->data['amountError'] = 'You don\'t have enough cash!';
                    }
                    elseif( $user->depositedToday + $amount > GAME_MAX_DEPOSIT )
                    {
                        $this->data['amountError'] = 'You can deposit at most &euro;' . GAME_MAX_DEPOSIT . ' per day. You have already deposited &euro;' . $user->depositedToday . ' today.';
                    }
                    else
                    {
                        $user->cash -= $amount;
                        $user->bank += $amount;
                        $user->depositedToday += $amount;

                        $updateArray = [
                            'cash' => $user->cash,
                            'bank' => $user->bank,
                            'depositedToday' => $user->depositedToday,
                        ];

                        $this->userModel->updateById($user->id, $updateArray);
                        $this->data['amount'] = null;

                        flash( 'bank_action', 'You have deposited <strong>&euro;' . $amount . '</strong>!' );
                    }
                }
                elseif( isset($_POST['withdraw']) )
                {
                    if( $user->bank < $amount )
                    {
                        $this->data['amountError'] = 'You don\'t have enough money in the bank!';
                    }
                    else
                    {
                        $user->cash += $amount;
                        $user->bank -= $amount;

                        $updateArray = [
                            'cash' => $user->cash,
                            'bank' => $user->bank
                        ];

                        $this->userModel->updateById( $user->id, $updateArray );
                        $this->data['amount'] = null;

                        flash( 'bank_action', 'You have withdrawn <strong>&euro;' . $amount . '</strong>!' );
                    }
                }
            }

            $this->view( 'locations/bank', $this->data );
        }


        /**
         * 
         * 
         * Hospital
         * @return Void
         * 
         * 
         */
        public function hospital (): Void
        {
            $user = &$this->data['user'];

            $userHasProperties = $this->propertyModel->existsByUserId( $user->id );
            $this->data['userHasProperties'] = $userHasProperties;

            if( isset($_POST['rest']) )
            {
                if( $userHasProperties )
                {
                    redirect( 'profile' );
                }
                else
                {
                    $duration = 5 * 60;
                    $reason = 'resting';

                    $this->userModel->hospitalize( $user->id, $duration, $reason );

                    redirect( 'hospitalized' );
                }
            }

            $this->view( 'locations/hospital', $this->data );
        }



        /**
         * 
         * 
         * Location: Harry's Hoovers
         * @return Void
         * 
         * 
         */
        public function Hoovers (): Void
        {
            $user = &$this->data['user'];

            if( isset($_POST['work']) )
            {
                if( isset($user->workingUntil)
                    || $user->energy < 20 )
                {
                    // The user is already working or his energy is too low, this shouldn't be possible
                    redirect( 'profile' );
                }
                else
                {
                    $futureTimestamp = new \DateTime();
                    $futureTimestamp->modify( '+15 minutes' );

                    $user->workingUntil = $futureTimestamp->format( 'Y-m-d H:i:s' );
                    $user->energy -= 20;

                    $updateArray = [
                        'workingUntil' => $user->workingUntil,
                        'energy' => $user->energy
                    ];

                    $this->userModel->updateById( $user->id, $updateArray );

                    flash( 'hoovers_work', 'You\'re working until ' . $futureTimestamp->format('H:i:s') . '!' );
                }
            }

            if( isset($_POST['launder']) )
            {
                $_POST = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
                $amount = $_POST['amount'];

                if( $amount > $user->cash )
                {
                    $this->data['amountError'] = 'You don\'t have enough cash available.';
                }
                elseif( $amount < 10 )
                {
                    $this->data['amountError'] = 'Laundering less than $10 isn\'t worth it.';
                }
                else
                {
                    $reducedAmount = floor( $amount * 0.8 );

                    $user->bank += $reducedAmount;
                    $user->cash -= $amount;

                    $updateArray = [
                        'cash' => $user->cash,
                        'bank' => $user->bank
                    ];

                    $this->userModel->updateById( $user->id, $updateArray );

                    flash( 'hoovers_launder', 'You\'ve laundered $' . $amount . ' and received $' . $reducedAmount . ' on your bankaccount.' );
                }
            }

            $this->view( 'locations/hoovers', $this->data );
        }


        /**
         * 
         * 
         * realEstate
         * @return Void
         * 
         * 
         */
        public function realEstate(): Void
        {
            $user = &$this->data['user'];

            $propertyCategories = $this->propertyCategoryModel->get( true );

            if( $_SERVER['REQUEST_METHOD'] == 'POST' )
            {
                // Sanitize POST data
                $_POST = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

                if( ! isset($_POST['propertyCategoryId']) )
                {
                    redirect('profile');
                }
                $propertyCategoryId = (int) $_POST['propertyCategoryId'];

                $paymentByBank = true;
                if( ! isset($_POST['payByBank']) )
                {
                    $paymentByBank = false;
                }

                if( ! isset($propertyCategories[$propertyCategoryId]) )
                {
                    redirect('profile');
                }

                $propertyCategory = $propertyCategories[$propertyCategoryId];

                if(
                    $paymentByBank == false 
                    && ! $propertyCategory->allowPaymentByCash
                ) {
                    //wrong payment type
                    redirect('profile');
                }


                if( $paymentByBank == true )
                {
                    if( $propertyCategory->price > $user->bank )
                    {
                        $error = 'You don\'t have enough money in your bank account!';
                    }
                    else 
                    {
                        $user->bank -= $propertyCategory->price; 
                    }
                }
                else
                {
                    if( $propertyCategory->price > $user->cash )
                    {
                        $error = 'You don\'t have enough cash money!';
                    }
                    else 
                    {
                        $user->cash -= $propertyCategory->price; 
                    }
                }

                if( empty($error) )
                {
                    $updateArray = [
                        'cash' => $user->cash,
                        'bank' => $user->bank
                    ];

                    $this->userModel->updateById( $user->id, $updateArray );

                    $insertArray = [
                        'userId' => $user->id,
                        'propertyCategoryId' => $propertyCategoryId
                    ];

                    $propertyId = $this->propertyModel->insert( $insertArray, true );

                    flash( 'realEstate_buy', 'You\'ve successfully bought a ' . $propertyCategory->name . '! Have fun!' );
                    redirect( 'properties/' . $propertyId );
                }
                else
                {
                    flash( 'realEstate_buy', $error, 'alert alert-danger' );
                }
            }

            $this->data['propertyCategories'] = $propertyCategories;
            $this->view( 'locations/realEstate', $this->data );
        }
    }