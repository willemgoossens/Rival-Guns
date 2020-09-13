<?php
    class PropertyCategory extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('propertycategories');
        }
    }