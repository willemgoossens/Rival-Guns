<?php
    class CrimeCategory extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('crimecategories');
        }

    }