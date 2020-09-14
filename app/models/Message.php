<?php
    class Message extends Model 
    {

        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('messages');
        }


        /**
         * 
         * 
         * getConversationIdsInWhichUserIsInvolved
         * @param Int userId
         * @return Array set of Ids
         * 
         * 
         */
        public function getConversationIdInWhichUserIsInvolved(Int $userId): ?Array
        {
            $this->db->query("SELECT conversationId
                                  FROM messages
                                  WHERE conversationId IN (
                                      SELECT conversationId
                                      FROM users_conversations
                                      WHERE userid = :userId
                                  )");

            $this->db->bind(':userId', $userId);

            return $this->db->resultSetArray();
        }

        
        /**
         * 
         * 
         * readMessagesForConversation
         * @param Int messageId
         * @param Int userId
         * @return Bool
         * 
         * 
         */
        public function readMessageForConversation(Int $messageId, Int $userId): Bool
        {
            $this->db->query("DELETE FROM users_unread_messages
                                  WHERE messageid = :messageId
                                      AND userid = :userId");

            $this->db->bind(":messageId", $messageId);
            $this->db->bind(":userId", $userId);

            return $this->db->execute();
        }


        /**
         * 
         * 
         * insertUnreadConnections
         * @param Array insertData
         * @return Bool
         * 
         * 
         */
        public function insertUnreadConnections(Array $insertData): Bool
        {
            $insertString = "";
            $insertValues = [];
            foreach($insertData as $values)
            {
                $insertString .= "(?, ?, ?),";
                $insertValues[ count($insertValues) + 1] = $values["userId"];
                $insertValues[ count($insertValues) + 1] = $values["conversationId"];
                $insertValues[ count($insertValues) + 1] = $values["messageId"];
            }
            $insertString = rtrim($insertString, ",");

            $this->db->query("INSERT INTO users_unread_messages
                                  (userid, conversationid, messageid)
                                  VALUES " . $insertString);

            $this->db->bindArray($insertValues);

            return $this->db->execute();
        }


        /**
         * 
         * 
         * creatMessage
         * @param Array messageData
         * @return Bool
         */
        public function createMessage(Array $messageData): Bool
        {
            $messageId = $this->insert($messageData, true);

            if( isset($messageData['userId']) )
            {
                $this->db->query("SELECT userid
                                    FROM users_conversations
                                    WHERE conversationid = :conversationId
                                        AND userid != :userId");

                $this->db->bind(":userId", $messageData['userId']);
            }
            else
            {
                $this->db->query("SELECT userid
                                    FROM users_conversations
                                    WHERE conversationid = :conversationId");
            }

            $this->db->bind(":conversationId", $messageData['conversationId']);

            $conversationPartners = $this->db->resultSetArray();

            $insertUnreads = [];
            foreach( $conversationPartners as $id )
            {
                $addArray = [
                    "userId" => $id,
                    "conversationId" => $messageData['conversationId'],
                    "messageId" => $messageId
                ];
                array_push($insertUnreads, $addArray);
            }

            return $this->insertUnreadConnections($insertUnreads);
        }

        /**
         * 
         * 
         * isUnreadForUser
         * @param Int messageId
         * @param Int userId
         * @return Int
         * 
         * 
         */
        public function isUnreadForUser(Int $messageId, Int $userId): Int
        {
            $this->db->query("SELECT *
                                FROM users_unread_messages
                                WHERE messageid = :messageId
                                    AND userid = :userId");

            $this->db->bind(":messageId", $messageId);
            $this->db->bind(":userId", $userId);

            return $this->db->rowCount();
        }

    }
