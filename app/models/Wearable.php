<?php
    class Wearable extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('wearables');
        }
    }