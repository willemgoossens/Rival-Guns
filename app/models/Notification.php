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
         * @param int userId
         * @return void
         * 
         * 
         */
        public function deleteOldNotifications(int $userId): void
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
         * @param int userId
         * @return int array
         * 
         * 
         */
        public function getUnreadNotifications(int $userId): array
        {
            $count = $this->getByUserIdAndReadAt($userId, NULL);
            return $count;
        }


        /**
         * 
         * 
         * readForUser
         * @param int userId
         * @param int offset
         * @param int limit
         * @return boolean success
         * 
         */
        public function readForUser(int $userId): bool
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
         * @param array notifications
         * @return boolean success
         * 
         * 
         */
        public function readForNotificationIds(array $notifications): bool
        {
            if(empty($notifications))
            {
                return false;
            }
            
            $now = new DateTime;
            $now = $now->format('Y-m-d H:i:s');

            foreach($notifications as $notification)
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
         * @param int userId
         * @param string body
         * @param string link
         * @param string class
         * 
         * @return boolean success
         * 
         * 
         */
        public function add(int $userId, string $body, string $link = '#', string $class = 'alert-primary'): boolean
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