<?php
  class ConversationReport extends Model
  {

    public function __construct()
    {
      $this->db = new Database;
      $this->setTableName('conversationreports');
    }

    /*
    *
    *
    * Get Conversation Reports
    * PARAMS: Classified
    *
    *
    */
    public function getConversationReports(int $offset, int $length, bool $classified){
      $this->db->query("SELECT *
                        FROM conversationReports
                        WHERE classified = :classified
                        ORDER BY createdAt DESC
                        LIMIT :offset, :length");
      // Bind values
      $this->db->bind(':classified', $classified);
      $this->db->bind(':length', $length);
      $this->db->bind(':offset', $offset);

      $row = $this->db->resultSet(true);

      if($row){
        return $row;
      }else{
        return false;
      }
    }

    /*
    *
    *
    * Does Report For Conversation And User Exist
    * PARAMS: Conversation ID, $reportedByID, (optional) $includeClassified = 0
    *
    *
    */
    public function doesReportForConversationAndUserExist(int $conversationId, int $reportedById){
      return $this->existsByConversationIdAndReportedById($conversationId, $reportedById);
    }
  }
