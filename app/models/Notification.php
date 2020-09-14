<?php
    class Notification extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('notifications');
        }


        /**
         * 
         * 
         * deleteOldNotifications
         * @param Int userId
         * @return Void
         * 
         * 
         */
        public function deleteOldNotifications(Int $userId): Void
        {
            $this->db->query("DELETE
                                FROM " . $this->getTableName() . "
                                WHERE userId = :userId
                                    AND readAt IS NOT NULL
                                    AND readAt < DATE_SUB(NOW(), INTERVAL 48 HOUR)
                            ");

            $this->db->bind(":userId", $userId);

            $this->db->execute();
        }


        /**
         * 
         * 
         * getUnreadNotifications
         * @param Int userId
         * @return Array
         * 
         * 
         */
        public function getUnreadNotifications(Int $userId): ?Array
        {
            $return = $this->getByUserIdAndReadAt($userId, NULL);
            return $return;
        }


        /**
         * 
         * 
         * readForUser
         * @param Int userId
         * @param Int offset
         * @param Int limit
         * @return Boolean success
         * 
         */
        public function readForUser (Int $userId): Bool
        {
            $this->db->query('UPDATE ' . $this->getTableName() . '
                                SET readAt = NOW()
                                WHERE userId = :userId');

            $this->db->bind(":userId", $userId);

            return $this->db->execute();
        }


        /**
         * 
         * 
         * readForNotificationIds
         * @param Array notifications
         * @return Bool success
         * 
         * 
         */
        public function readForNotificationIds(Array $notifications): Bool
        {
            if( empty($notifications) )
            {
                return false;
            }
            
            $now = new DateTime;
            $now = $now->format('Y-m-d H:i:s');

            foreach( $notifications as $notification )
            {
                $updateArray = [
                    'readAt' => $now
                ];
                $this->updateById($notification->id, $updateArray);
            }

            return true;
        }


        /**
         * 
         * 
         * add
         * @param Int userId
         * @param String body
         * @param String link
         * @param String class
         * 
         * @return Boolean success
         * 
         * 
         */
        public function add(Int $userId, String $body, String $link = '#', String $class = 'alert-primary'): Bool
        {
            $insert = [
                'userId' => $userId,
                'body' => $body,
                'link' => $link,
                'class' => $class
            ];

            return $this->insert($insert);
        }
    }