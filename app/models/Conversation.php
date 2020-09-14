<?php
    class Conversation extends Model
    {
        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('conversations');

            $this->messageModel = $this->model('Message');
            $this->userModel = $this->model('User');
        }


        /**
         * 
         * 
         * CountUnreadConversations
         * @param Int userId
         * @return Int
         * 
         * 
         */
        public function countUnreadConversations (Int $userId): Int
        {
          $this->db->query("SELECT *
                              FROM users_unread_messages
                              WHERE userid = :userId
                              GROUP BY conversationid");
          $this->db->bind(':userId', $userId);

          return $this->db->rowCount();
        }

        
        /**
         * 
         * 
         * getIfConversationsHasUnreadMessagesForUser
         * @param Int UserId
         * @param Int conversationId
         * @return Bool
         * 
         * 
         */
        public function getIfConversationHasUnreadMessageForUser (Int $userId, Int $conversationId): Bool
        {
            $this->limit(1)
                  ->db->query("SELECT *
                                  FROM users_unread_messages
                                  WHERE userid = :userId
                                    AND conversationid = :conversationId");
            $this->db->bind(':userId', $userId);
            $this->db->bind(':conversationId', $conversationId);
            
            if( $this->db->rowCount() > 0 )
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * 
         * 
         * getLatestUpdatedConversationsByParticipantId
         * @param Int userId
         * @param Int limit
         * @param Int offset
         * @return Array
         * 
         * 
         */
        public function getLatestUpdatedConversationsByParticipantId(Int $userId, Int $limit, Int $offset): ?Array
        {
            $conversationIds = $this->messageModel
                                    ->limit($limit)
                                    ->offset($offset)
                                    ->orderBy("MAX(createdAt)", "DESC")
                                    ->groupBy("conversationId")
                                    ->getConversationIdInWhichUserIsInvolved($userId);

            $conversations = [];
            if(! empty($conversationIds) )
            {        
                $conversations = $this->orderBy("id", "FIELD", $conversationIds)
                                      ->getById($conversationIds);
            }

            return $conversations;
        }

        /**
         * 
         * 
         * insertConnections
         * @param Array insertData
         * @return Bool
         */
        public function insertConnections (Array $insertData): Bool
        {
            $insertString = "";
            $insertValues = [];
            foreach( $insertData as $values )
            {
                $insertString .= "(?, ?),";
                $insertValues[ count($insertValues) + 1] = $values["userId"];
                $insertValues[ count($insertValues) + 1] = $values["conversationId"];
            }
            $insertString = rtrim($insertString, ",");

            $this->db->query("INSERT INTO users_conversations
                                (userid, conversationid)
                                VALUES " . $insertString);

            $this->db->bindArray($insertValues);

            return $this->db->execute();
        }


        /**
        *
        *
        * Check if the user is a participant
        * @param Int conversationId
        * @param Int userId
        * @return Int
        *
        *
        */
        public function checkIfUserIsParticipant (Int $conversationId, Int $userId): Int
        {
          $this->db->query("SELECT *
                              FROM users_conversations
                              WHERE conversationid = :conversationId
                                AND userid = :userId");

          $this->db->bind(':conversationId', $conversationId);
          $this->db->bind(':userId', $userId);

          return $this->db->rowCount();
        }


        /**
         * 
         * 
         * This function gets all people in a conversation
         * @param Int conversationID
         * @return Array An array with all user IDs
         */
        public function getPeopleInConversation(int $conversationId): array
        {
            $this->db->query("SELECT userId
                                FROM users_conversations
                                WHERE conversationId = :conversationId");
            
            $this->db->bind(":conversationId", $conversationId);

            $userIds = $this->db->resultSetArray();

            return $this->userModel->getFlaggedUniqueById($userIds);
        }


        /**
         * 
         * 
         * This function adds the conversation, connections and the first message
         * @param  Array  $conversationInformation Array with conversation input
         * @param  String $messageBody             The message body, will be added to the message
         * @param  Array  $participantIds          A list of all participant Ids, EXCLUDING the sender
         * @return Bool                            [description]
         * 
         * 
         */
        public function addConversation(Array $conversationInformation, String $messageBody, Array $participantIds): Bool
        {
            $conversationId = $this->insert($conversationInformation, true);
            if( ! $conversationId )
            {
                die("Something went wrong while creating the conversation");
            }

            // Adding the connections to the users
            $userConversationConnections = array_map(
                function($val) use ($conversationId) 
                {
                    return ['userId' => $val, 'conversationId' => $conversationId];
                }, $participantIds);

            if( isset($conversationInformation['userId']) )
            {
                array_push($userConversationConnections, ['userId' => $conversationInformation['userId'], 'conversationId' => $conversationId]);
            }
            
            if( ! $this->insertConnections($userConversationConnections) )
            {
                die("Something went wrong while creating the connections for the conversations");
            }

            // And create the message!
            $insertMessageData = [
                'conversationId' => $conversationId,
                'body' => $messageBody,
                'userId' => $conversationInformation['userId'] ?? null
            ];

            $this->messageModel->createMessage($insertMessageData);

            return true;
        }

    }
