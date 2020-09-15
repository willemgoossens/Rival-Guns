<?php
    namespace App\Controllers;
    use App\Libraries\Controller as Controller;
    
    class Punishments extends Controller
    {
        /**
         * 
         * 
         * permanentBan
         * @return Void
         * 
         * 
         */
        public function permanentBan (): void
        {
            echo "You've received a permanent ban.";
        }


        /**
         * 
         * 
         * temporaryBan
         * @return Void
         * 
         * 
         */
        public function temporaryBan (): void
        {
            echo "You've received a temporary ban.";
        }

    }