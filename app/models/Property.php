<?php
    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;
    
    class Property extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('properties');
        }


        /**
         * 
         * 
         * calculateProfitsForUserAndTime
         * @param Int userId
         * @param DateTime time
         * @return Void
         * 
         * 
         */
        public function calculateProfitsForUserAndTime( Int $userId, \DateTime $time ): Void
        {
            $properties = $this->getByUserIdAndNotBusinessCategoryIdAndInstallingUntil( $userId, NULL, NULL );

            $businessCategoriesIds = array_column( $properties, "businessCategoryId" );
            $businessCategories = $this->businessCategoryModel->getFlaggedUniqueById( $businessCategoriesIds );

            $user = $this->userModel->getSingleById( $userId );
            $user->lastCheckedAt = new \DateTime( $user->lastCheckedAt );

            foreach ($properties as $property) 
            {
                if( isset( $property->businessCategoryId ) )
                {
                    $businessCategory = $businessCategories[$property->businessCategoryId];
                    $profitPerSecond = $businessCategory->profitPerDay / ( 24 * 60 * 60 );

                    $profit = ( $time->getTimestamp() - $user->lastCheckedAt->getTimestamp() ) * $profitPerSecond;

                    if( $businessCategory->isLegal )
                    {
                        $user->bank += $profit;
                    }
                    else
                    {
                        $user->cash += $profit;
                    }
                }
            }

            $updateArray = [
                'cash' => $user->cash,
                'bank' => $user->bank
            ];

            $this->userModel->updateById( $userId, $updateArray );
        }


        /**
         * 
         * 
         * finishInstallationsForUserAndTime
         * @param Int userId
         * @param DateTime dateTime
         * @return Void
         * 
         * 
         */
        public function finishInstallationForUserAndTime( Int $userId, \DateTime $dateTime ): Void
        {
            $this->db->query( "SELECT id 
                                FROM " . $this->getTableName() . "
                                WHERE userId = :userId
                                AND installingUntil <= :installingUntil" );
            $this->db->bind( ":userId", $userId );
            $this->db->bind( ":installingUntil", $dateTime->format( 'Y-m-d H:i:s' ) );
            $property = $this->db->single();
            
            if( $property )
            {
                $this->updateById( $property->id, [ 'installingUntil' => NULL ] );
            }
        }


        /**
         * 
         * 
         * confiscatePropertiesForPriceAndUser
         * @param Int price
         * @param Int userId
         * @param Null|DateTime datetime
         * @return Void
         * 
         */
        public function confiscatePropertiesForPriceAndUser( Int $price, Int $userId, \DateTime $dateTime = null ): Void
        {
            $this->db->query("SELECT properties.id, propertycategories.name, @total := @total + propertyCategories.price
                                FROM (properties, (select @total := 0) t)
                                JOIN (SELECT * FROM propertyCategories ORDER BY price ASC) propertycategories
                                    ON properties.propertyCategoryId = propertyCategories.id
                                WHERE
                                    properties.userId = :userId
                                    AND @total < :price");
            $this->db->bind(":price", $price);
            $this->db->bind(":userId", $userId);

            $properties = $this->db->resultSet();
            
            if( empty( $properties ) )
            {
                return;
            }

            $countedProperties = array_count_values( array_column( $properties, "name" ) );
            $confiscatedString = "";
            foreach( $countedProperties as $name => $amount )
            {
                $confiscatedString .= $amount . "x " . $name . ", ";
            }
            $confiscatedString = rtrim( $confiscatedString, ", " );
            
            $notificationText = "As your bank account didn't contain enough money. The government confiscated some of your properties (" . $confiscatedString . ").";
            $insertNotificationArray = [
                'body' => $notificationText,
                'class' => 'alert alert-warning',
                'userId' => $userId,
                'createdAt' => isset( $dateTime ) ? $dateTime->format( 'Y-m-d H:i:s' ) : 'NOW()'
            ];
            $this->notificationModel->insert( $insertNotificationArray );

            foreach( $properties as $property )
            {
                $this->propertyModel->confiscateById( $property->id, $dateTime );
            }
        }


        /**
         * 
         * 
         * confiscateById
         * @param Int propertyId
         * @param Null|DateTime datetime
         * @param Void
         * 
         * 
         */
        public function confiscateById( Int $propertyId, \DateTime $dateTime = null ): Void
        {
            $property = $this->getSingleById( $propertyId );
            
            if( isset( $property->businessCategoryId ) )
            {
                $businessCategory = $this->businessCategoryModel->getSingleById( $property->businessCategoryId );

                if( ! $businessCategory->isLegal )
                {
                    $insertArray = [
                        'userId' => $property->userId,
                        'type' => $businessCategory->notLegalCrimeTypeId
                    ];
                    $this->criminalRecordModel->insert( $insertArray );

                    $crimeType = $this->crimeTypeModel->getSingleById( $businessCategory->notLegalCrimeTypeId );

                    $this->userModel->arrest( $property->userId );

                    $insertNotificationArray = [
                        'body' => "While confiscating one of your properties (#" . $propertyId . "), the police found a " . $businessCategory->name . ". As such, you've also been arrested for " . $crimeType->name . ".",
                        'class' => "alert alert-danger",
                        'userId' =>  $property->userId,
                        'createdAt' => isset( $dateTime ) ? $dateTime->format( 'Y-m-d H:i:s' ) : 'NOW()'
                    ];

                    $this->notificationModel->insert( $insertNotificationArray );
                }
            }

            $this->deleteById( $propertyId );
        }
    }
