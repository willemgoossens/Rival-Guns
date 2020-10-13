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
         * @param Int propertyCategoryId
         * @return Array businessCategories
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


        /**
         * 
         * 
         * getPropertyTypeForConfiscationForPriceAndCategoryIds
         * @param Int price
         * @param Array categoryIds
         * @return Mixed rows
         * 
         * 
         */
        public function getPropertyTypeForConfiscationForPriceAndCategoryIds( Int $difference, Array $categoryIds): Mixed
        {
            $this->db->query("SELECT * 
                                FROM " . $this->getTableName() . "
                                WHERE 
                                    id IN :categoryIds
                                    AND price >= :difference
                                ORDER BY price ASC
                                LIMIT 1");
            $this->db->bind(":difference", $difference);
            $categoryIds = "(" . implode(",", $categoryIds) . ")";
            $this->db->bind(":categoryIds", $categoryIds);
            
            $category = $this->db->single();

            if( ! $category )
            {
                $this->db->query("SELECT * 
                                FROM " . $this->getTableName() . "
                                WHERE 
                                    id IN :categoryIds
                                    AND price < :difference
                                ORDER BY price DESC
                                LIMIT 2");
                $this->db->bind(":difference", $difference);
                $this->db->bind(":categoryIds", $categoryIds);
                
                $category = $this->db->resultSet( true );
            }

            return $categoryId;
        }
    }