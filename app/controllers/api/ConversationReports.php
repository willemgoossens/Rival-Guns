<?php
    namespace App\Controllers\Api;
    use App\Libraries\REST_Controller as REST_Controller;
    
    class ConversationReports extends REST_Controller
    {
        /**
         * 
         * 
         * getMessages
         * @param Int conversationId
         * @param Int offset
         * @return String response
         * 
         * 
         */
        public function getMessages(Int $conversationId, Int $offset): String
        {
            // Check for GET request
            // This is the only option
            if( $_SERVER['REQUEST_METHOD'] == 'GET' )
            {
                // Don't forget, our GET data has already been cleaned in the core.php
                // Check if we have the required input variables
                if(
                    ! isset($_SESSION['userId'])
                    || ! isset($conversationId)
                    || ! isset($offset)
                ) {
                    // If we don't have everything, return a bad request
                    $this->response([""], 400);
                }

                $userId = $_SESSION['userId'];
                $user = $this->userModel->getSingleById( $userId, 'adminRole' );
                
                $adminRights = $this->adminRoleModel->getRightNamesForRole( $user->adminRole );

                // Check if the user has the correct rights
                if( ! in_array("HandleReportedConversations", $adminRights) )
                {
                    // Return a bad request
                    $this->response([], 400);
                }

                // Get messages
                $messages = $this->messageModel->offset( (int) $offset )
                                              ->limit( 8 )
                                              ->orderBy( "createdAt", "DESC" )
                                              ->getByConversationId( $conversationId );

                foreach( $messages as &$message )
                {
                    // We need to convert the Markdown to HTML
                    $message->body = MarkdownToHTML( $message->body );
                    // We also need to conversation partner's name for the messages that had not been send by the user
                    $conversationPartner = $this->userModel->getSingleById( $message->userId, 'name' );
                    $message->name = $conversationPartner->name;
                }



                // The return string
                $data = [
                    'messages' => $messages
                ];

                // Return the response
                $this->response( $data, 200 );
            }
            else {
              // Otherwise return error
              $this->response( [], 405 );
            }
        }
    }
