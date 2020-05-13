<?php
  class Test extends Model
  {
    public function __construct() {
      $this->db = new Database;
      $this->setTableName("users");
    }
  }
