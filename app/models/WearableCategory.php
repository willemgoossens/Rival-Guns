<?php
    class WearableCategory extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('wearablecategories');
        }
    }