<?php
    class CrimeType extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('crimetypes');
        }
    }