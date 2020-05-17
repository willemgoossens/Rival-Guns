<?php
    class Hospitalization extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('hospitalizations');
        }
    }