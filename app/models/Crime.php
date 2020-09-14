<?php
    class Crime extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('crimes');
        }

    }
