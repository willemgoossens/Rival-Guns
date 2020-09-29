<?php
    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;
    
    class PropertyCategory extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('propertycategories');
        }

        /**
         * 
         * 
         * getBusinessCategoriesForId
         * @param int propertyCategoryId
         * @return array businessCategories
         * 
         * 
         */
        public function getBusinessCategoryIdsForId ( Int $propertyCategoryId ): Array
        {
            $this->db->query("SELECT businessCategoryId
                                        FROM propertycategories_businesscategories
                                        WHERE propertyCategoryId = :propertyCategoryId");
            $this->db->bind( ':propertyCategoryId', $propertyCategoryId );

            $categoryIds = $this->db->resultSetArray();

            return $categoryIds;
        }
    }