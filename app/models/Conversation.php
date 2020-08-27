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
        * count unread conversations for a specific user
        * PARAM: int - userId
        *
        *
        ****************************/
        public function countUnreadConversations(int $userId)
        {
          $this->db->query("SELECT *
                        FROM users_unread_messages
                        WHERE userid = :userId
                        GROUP BY conversationid");
          // Bind Values
          $this->db->bind(':userId', $userId);
          // Rowcount
          return $this->db->rowCount();
        }

        /*************************
        *
        *
        * check if conversation is unread for specific user
        * PARAM: int - userId
        * PARAM: int - conversationId
        *
        *
        ****************************/
        public function getIfConversationHasUnreadMessageForUser(int $userId, int $conversationId)
        {
          $this->limit(1)->db->query("SELECT *
                                      FROM users_unread_messages
                                      WHERE userid = :userId
                                        AND conversationid = :conversationId");
          // Bind Values
          $this->db->bind(':userId', $userId);
          $this->db->bind(':conversationId', $conversationId);
          // Rowcount
          if($this->db->rowCount() > 0)
          {
            return true;
          }
          else
          {
            return false;
          }
        }

        /*************************
        *
        *
        * get the latest conversations for a specific user
        * PARAM: int - userId
        *
        *
        ****************************/
        public function getLatestUpdatedConversationsByParticipantId(int $userId, int $limit, int $offset)
        {
          $conversationIds = $this->messageModel
                                  ->limit($limit)
                                  ->offset($offset)
                                  ->orderBy("MAX(createdAt)", "DESC")
                                  ->groupBy("conversationId")
                                  ->getConversationIdInWhichUserIsInvolved($userId);

          $conversations = [];
          if(! empty($conversationIds))
          {        
            $conversations = $this->orderBy("id", "FIELD", $conversationIds)
                                  ->getById($conversationIds);
          }

          return $conversations;
        }

        /*************************
        *
        *
        * insert user conversation connections
        * PARAM: array - insert
        *
        *
        ****************************/
        public function insertConnections(array $insertData): bool
        {
          $insertString = "";
          $insertValues = [];
          foreach($insertData as $values)
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
        * @param: int conversationId
        * @param: int user id
        *
        *
        **********************************/
        public function checkIfUserIsParticipant(int $conversationId, int $userId)
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
         * This function gets all people in a conversation
         * @param  int   $conversationId
         * @return array                 An array with all userIds
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
         * This function adds the conversation, connections and the first message
         * @param  array  $conversationInformation Array with conversation input
         * @param  string $messageBody             The message body, will be added to the message
         * @param  array  $participantIds          A list of all participant Ids, EXCLUDING the sender
         * @return bool                            [description]
         */
        public function addConversation(array $conversationInformation, string $messageBody, array $participantIds): bool
        {
            $conversationId = $this->insert($conversationInformation, true);
            if(!$conversationId)
            {
                die("Something went wrong while creating the conversation");
            }

            // Adding the connections to the users
            $userConversationConnections = array_map(function($val) use ($conversationId) 
                                            {
                                                return ['userId' => $val, 'conversationId' => $conversationId];
                                            }, $participantIds);

            if( isset($conversationInformation['userId']))
            {
              array_push($userConversationConnections, ['userId' => $conversationInformation['userId'], 'conversationId' => $conversationId]);
            }
            
            if(! $this->insertConnections($userConversationConnections))
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
