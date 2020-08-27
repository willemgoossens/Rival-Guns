<?php
  class Message extends Model {

    public function __construct()
    {
      $this->db = new Database;
      $this->setTableName('messages');
    }

    /***************************************
    *
    * get All the messages in which a user is involved
    * @param: userId
    *
    ****************************************/
    public function getConversationIdInWhichUserIsInvolved(int $userId)
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

    /***************************************
    *
    * read messages for conversation
    * @param: int - conversation
    * @param: int - user id
    *
    ****************************************/
    public function readMessageForConversation(int $messageId, int $userId)
    {
      $this->db->query("DELETE FROM users_unread_messages
                        WHERE messageid = :messageId
                          AND userid = :userId");

      $this->db->bind(":messageId", $messageId);
      $this->db->bind(":userId", $userId);

      return $this->db->execute();
    }

    /*************************
    *
    *
    * insert user conversation connections
    * PARAM: array - insert
    *
    *
    ****************************/
    public function insertUnreadConnections(array $insertData): bool
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

    /*************************
    *
    *
    * create new message
    * PARAM: array - messageData
    *
    *
    ****************************/
    public function createMessage(array $messageData): bool
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
      foreach($conversationPartners as $id)
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

    /***************************************
    *
    * is unread for user
    * @param: int - message Id
    * @param: int - user id
    *
    ****************************************/
    public function isUnreadForUser(int $messageId, int $userId)
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
