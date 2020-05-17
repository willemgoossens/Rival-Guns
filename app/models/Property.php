<?php
    class Property extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('properties');
        }
    }
