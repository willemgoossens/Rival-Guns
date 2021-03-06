<?php

    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;

    class FutureImprisonment extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->dateTimeColumns = ['imprisonedFrom', 'createdAt'];
            $this->setTableName('futureimprisonments');
        }


        /**
         * 
         * 
         * createFutureImprisonmentForLaunderingOrNotForUser
         * @param Int userId
         * @param Void
         * 
         * 
         */
        public function createFutureImprisonmentForLaunderingOrNotForUser( Int $userId ): Void
        {
            $logs = $this->launderingLogModel->getByUserId( $userId );

            if( empty( $logs ) )
            {
                return;
            }

            $now = new \Datetime;
            $orderedLogs = array_fill( 1, 14, 0 );

            foreach( $logs as $log )
            {
                $log->originalChanceFactor = ( $log->launderedAmount / $log->maxLaunderingAmount ) ** 2.5;

                $key = 14 - $now->diff( $log->createdAt )->format( '%a' );
                $log->chanceFactor = ( $key / 14 ) * $log->originalChanceFactor;

                $orderedLogs[$key] = $log;
            }

            $remainingHours = $now->format( 'H' ) + 1;
            $futureDays = 0;

            while( true )
            {
                if( $remainingHours > 23 )
                {
                    $futureDays += 1;
                    $remainingHours = 0;

                    unset( $orderedLogs[1] );

                    if( empty( $orderedLogs ) )
                    {
                        break;
                    }

                    // We need to reset the keys so that the first key is 1
                    $orderedLogs = array_combine( range( 1, count($orderedLogs) ), array_values( $orderedLogs ) );

                    foreach( $orderedLogs as $key => &$log )
                    {
                        if( is_object($log) )
                        {
                            $log->chanceFactor = ( $key / 14 ) * $log->originalChanceFactor;
                            // We cap of the very small chances
                            if( $log->chanceFactor < 0.1 && $log->chanceFactor > 0 )
                            {
                                $log->chanceFactor = 0;
                            }
                        }
                    }
                }

                $chance = array_sum( array_column( $orderedLogs, "chanceFactor" ) ) * 100;
                $max_value = max( array_column( $orderedLogs, "chanceFactor" ) ) * 100;
                if( $chance  < 300 && $max_value < 100 )
                {
                    break;
                }

                if( $chance > rand(0, 100 * 14 * 24) )
                {
                    $imprisonedFrom = new \DateTime;
                    $imprisonedFrom->modify('+' . $futureDays . ' day');
                    $imprisonedFrom->modify('+' . $remainingHours . ' hour');
                    $imprisonedFrom->modify('+' . rand( 0, 59 ) . ' minute');
                    $imprisonedFrom->modify('+' . rand( 0, 59 ) . ' second');

                    $launderedAmount = array_sum( array_column( $orderedLogs, "launderedAmount" ) );
                    if( $launderedAmount < 10000)
                    {
                        $crimeType = "money laundering (minor)";
                    }
                    elseif( 
                        $launderedAmount >= 10000
                        && $launderedAmount < 750000
                    ) {
                        $crimeType = "money laundering (medium)";
                    }
                    elseif(
                        $launderedAmount >= 750000
                        && $launderedAmount < 5000000
                    ) {
                        $crimeType = "money laundering (maximum)";
                    }
                    else 
                    {
                        $crimeType = "money laundering (supermax)";
                    }

                    $crimeType = $this->crimeTypeModel->getSingleByName( $crimeType );

                    $fine = floor( $launderedAmount * ( rand( 40, 70 ) / 100 ) );

                    $insertArray = [
                        'userId' => $userId,
                        'department' => 'minimum',
                        'imprisonedFrom' => $imprisonedFrom->format('Y-m-d H:i:s'),
                        'crimeTypeId' => $crimeType->id,
                        'fine' => $fine
                    ];

                    $this->insert( $insertArray );

                    break;
                }

                $remainingHours++;
            }
        }


        /**
         * 
         * 
         * finishDueFutureImprisonmentsForUserAndTime
         * @param Int userId
         * @param DateTime time
         * @return Bool
         * 
         * 
         */
        public function finishDueFutureImprisonmentsForUserAndTime( Int $userId, \DateTime $dateTime ): Bool
        {
            $this->db->query( "SELECT * 
                                FROM " . $this->getTableName() . "
                                WHERE userId = :userId
                                AND imprisonedFrom <= :imprisonedFrom" );
            $this->db->bind( ":userId", $userId );
            $this->db->bind( ":imprisonedFrom", $dateTime->format( 'Y-m-d H:i:s' ) );
            $futureImprisonment = $this->db->single();
            
            if( ! $futureImprisonment )
            {
                return false;
            }

            $crimeType = $this->crimeTypeModel->getSingleById( $futureImprisonment->crimeTypeId );

            $insertCriminalRecordArray = [
                'userId' => $userId,
                'type' => $futureImprisonment->crimeTypeId
            ];
            $this->criminalRecordModel->insert( $insertCriminalRecordArray );
            $this->userModel->arrest( $userId );

            $imprisonedUntil = clone $futureImprisonment->imprisonedFrom;
            $imprisonedUntil->modify('+' . $crimeType->jailTime . ' second');

            $insertNotificationArray = [
                'body' => "The Tax Services have found you were laundering money and have convicted you to imprisonment until " . dateTimeFormat( $imprisonedUntil ) . " and a &euro;" . $futureImprisonment->fine . " fine. If you were already in prison, your sentence will be elongated.",
                'class' => "alert alert-danger",
                'userId' => $userId,
                'createdAt' => $futureImprisonment->imprisonedFrom->format( 'Y-m-d H:i:s ')
            ];

            $this->notificationModel->insert( $insertNotificationArray );

            $user = $this->userModel->getSingleById( $userId, 'bank' );

            if( $user->bank < $futureImprisonment->fine )
            {
                $updateUserArray = [
                    'bank' => 0
                ];
                $this->userModel->updateById( $userId, $updateUserArray );

                $difference = $futureImprisonment->fine - $user->bank;

                $this->propertyModel->confiscatePropertiesForPriceAndUser( $difference, $userId, $futureImprisonment->imprisonedFrom );
            }
            else
            {
                $user->bank -= $futureImprisonment->fine;
                $updateUserArray = [
                    'bank' => $user->bank
                ];
                $this->userModel->updateById( $userId, $updateUserArray );
            }

            $this->deleteById( $futureImprisonment->id );
            $this->launderingLogModel->deleteAllLaunderingLogsForUser( $user->id );

            return true;
        }


        /**
         * 
         * 
         * getFutureImprisonmentTimestampsForUser
         * @param Int userId
         * @return Null|Array
         * 
         * 
         */
        public function getFutureImprisonmentTimestampsForUser( Int $userId ): ?Array 
        {
            $futureImprisonments = $this->getByUserId( $userId );

            if( ! $futureImprisonments )
            {
                return null;
            }
            $timestamps = [];

            foreach( $futureImprisonments as $futureImprisonment)
            {
                array_push( $timestamps, $futureImprisonment->imprisonedFrom );
    
                $crimeType = $this->crimeTypeModel->getSingleById( $futureImprisonment->crimeTypeId );
                $futureImprisonment->imprisonedFrom->modify('+' . $crimeType->jailTime . ' second');
                array_push( $timestamps, $futureImprisonment->imprisonedFrom );
            }

            return $timestamps;
        }
    }