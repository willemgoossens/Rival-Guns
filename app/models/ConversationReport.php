<?php
    class ConversationReport extends Model
    {

        public function __construct()
        {
            $this->db = new Database;
            $this->setTableName('conversationreports');
        }

        /**
         * 
         * 
         * getConversationReports
         * @param Int offset
         * @param Int length
         * @param Bool classified
         * @return Mixed
         */
        public function getConversationReports(Int $offset, Int $length, Bool $classified)
        {
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

            if($row)
            {
                return $row;
            }
            else
            {
                return false;
            }
        }

        
        /**
         * 
         * 
         * doesReportForConversationAndUserExist
         * @param Int conversationId
         * @param Int reportedById
         * @return Bool
         * 
         * 
         */
        public function doesReportForConversationAndUserExist(Int $conversationId, Int $reportedById): Bool
        {
            return $this->existsByConversationIdAndReportedById($conversationId, $reportedById);
        }
    }
