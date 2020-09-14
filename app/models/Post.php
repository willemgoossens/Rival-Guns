<?php
    class Post extends Model 
    {
        public function __construct()
        {
          $this->db = new Database;
          $this->setTableName('posts');
        }

    }
