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
                echo "<br/>Property id:" . $property->id . "<br/>";
                if( isset( $property->businessCategoryId ) )
                {
                    echo "<br/>Property id:" . $property->id . "<br/>";
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
            echo $user->bank;

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
    }
