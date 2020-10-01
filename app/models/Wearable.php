<?php
    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;
    
    class Wearable extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('wearables');
        }


        /**
         * 
         * 
         * deleteIllegalEquippedWearablesForUser
         * @param Int userId
         * @return Void
         * 
         * 
         */
        public function deleteIllegalEquippedWearablesForUser( Int $userId ): Void
        {
            $wearables = $this->getByUserIdAndEquipped($userId, 1);
            if( ! empty($wearables) )
            {
                foreach( $wearables as $wearable )
                {
                    $isIllegal = $this->wearableCategoryModel->existsByIdAndIllegal($wearable->wearableCategoryId, 1);

                    if( $isIllegal )
                    {
                        $this->deleteById( $wearable->id );
                    }
                }
            }
        }

    }