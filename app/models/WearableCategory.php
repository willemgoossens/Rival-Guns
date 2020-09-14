<?php
    namespace App\Models;
    use App\Libraries\Model as Model;
    use App\Libraries\Database as Database;
    
    class WearableCategory extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('wearablecategories');
        }
    }