<?php
    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;
    
    class CrimeType extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('crimetypes');
        }
    }