<?php
    class Conversations extends Controller
    {
        public function __construct()
        {
            $this->userModel = $this->model('User');
            $this->adminRoleModel = $this->model('AdminRole');
            $this->messageModel = $this->model('Message');
            $this->conversationModel = $this->model('Conversation');
            $this->conversationReportModel = $this->model('ConversationReport');
            $this->notificationModel = $this->model('Notification');

            // Set the sessions for the nav bar
            $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
            $this->data['user']->adminRights         = $this->adminRoleModel->getRightNamesForRole($this->data['user']->adminRole);
            $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
            $this->data['user']->notifications = $this->notificationModel->getUnreadNotifications($_SESSION['userId']);
        }

        /**
        *
        *
        * Index
        * @param Int page
        * @return Void
        *
        *
        */
        public function index( Int $page = 1 ): Void
        {
            $user = &$this->data['user'];

            $this->data['conversations'] = '';
            $this->data['totalCases'] = $this->conversationModel->count();
            $this->data['page'] = $page;
            $this->data['casesPerPage'] = 7;

            // If the pagenumber is too high, reset to 1
            $currentStartingCase = $this->data['page'] * $this->data['casesPerPage'] - $this->data['totalCases'];
            if(
                $currentStartingCase > $this->data['casesPerPage'] 
                || $this->data['page'] < 0
            ) {
                $this->data['page'] = 1;
            }

            $offset = ( $this->data['page'] - 1 ) * $this->data['casesPerPage'];

            $this->data['conversations'] = $this->conversationModel
                                                ->getLatestUpdatedConversationsByParticipantId($user->id, $this->data['casesPerPage'], $offset);
            
            foreach( $this->data['conversations'] as &$conversation )
            {
                $conversation->unread = $this->conversationModel->getIfConversationHasUnreadMessageForUser($user->id, $conversation->id);
                $conversation->lastMessage = $this->messageModel
                                                  ->limit(1)
                                                  ->offset(0)
                                                  ->orderBy('createdAt', 'DESC')
                                                  ->getSingleByConversationId($conversation->id);
                                                  
                $conversation->lastMessage->senderName = NULL;              
                if( $conversation->lastMessage->userId != $user->id )
                {
                    $sender = $this->userModel->getSingleById( $user->id, 'name' );
                    if( isset($conversation->noReplySender) )
                    {
                        $conversation->lastMessage->senderName = $sender->name . ' on behalf of ' . $conversation->noReplySender;
                    }
                    else
                    {
                        $conversation->lastMessage->senderName = 'Last message sent by ' . $sender->name;
                    }
                }
            }
            // And show the view
            $this->view( 'conversations/index', $this->data );
        }

        /**
        *
        *
        * Add
        * @return Void
        *
        *
        */
        public function add(): Void
        {
            $user = &$this->data['user'];

            $this->data['body'] = '';
            $this->data['bodyError'] = NULL;
            $this->data['subject'] = '';
            $this->data['subjectError'] = NULL;
            $this->data['to'] = '';
            $this->data['toError'] = NULL;

            if( $_SERVER['REQUEST_METHOD'] == 'POST' )
            {
                // First we Convert this stuff to Markdown
                $_POST['body'] = HTMLToMarkdown($_POST['body']);
                // Note that we use filter_var_array here!
                $_POST = filter_var_array($_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                $this->data['body'] = $_POST['body'];
                $this->data['subject'] = $_POST['subject'];
                $this->data['to'] = $_POST['to'];

                // First validated the username
                if( empty($this->data['to']) )
                {
                    $this->data['toError'] = "Please enter a username.";
                }
                else
                {
                    $toIds = [];
                    foreach( $this->data['to'] as $to )
                    {
                        // Check if the user does exist
                        $to = $this->userModel->getSingleByName($this->data['to'], 'name', 'id');

                        if( empty( $to ) )
                        {
                            $this->data['toError'] = "This is not a valid username.";
                        }
                        // Oh, and the user can not send messages to himself
                        elseif($user->name == $to->name)
                        {
                            $this->data['toError'] = "You can't send messages to yourself.";
                        }
                        else
                        {
                            $this->data['toError'] = false;
                            array_push( $toIds, $to->id );
                        }
                    }
                }

                // Validate the subject
                if( empty( $this->data['subject'] ) )
                {
                    $this->data['subjectError'] = "Please enter a subject.";
                }
                else
                {
                    $this->data['subjectError'] = false;
                }

                // Validate the body
                if( strlen($_POST['body']) < 12 )
                {
                    $this->data['bodyError'] = 'Your message has to be at least 12 characters.';
                }
                else
                {
                    $this->data['bodyError'] = false;
                }

                if( 
                    empty($this->data['bodyError'])
                    && empty($this->data['toError'])
                    && empty($this->data['subjectError'])
                ) {
                    // Create a new conversation
                    $insertConversationData = [
                        'userId' => $user->id,
                        'subject' => $this->data['subject']
                    ];

                    $this->conversationModel->addConversation( $insertConversationData, $this->data['body'], $toIds );

                    flash( 'conversation_message', 'Your message has been send!' );
                    redirect( 'conversations' );
                }
                else
                {
                    // DELETE WHEN ADDING MULTIPLE RECEIVER INPUTS
                    $this->data['to'] = $_POST['to'][0];
                    $this->data['body'] = $this->data['body'];
                }
            }

            $this->view( 'conversations/add', $this->data );
        }

        /**
        *
        *
        * Read
        * @param Int conversationId
        * @return Void
        *
        */
        public function read( Int $conversationId ): Void
        {
            $user = &$this->data['user'];

            if( ! $this->conversationModel->checkIfUserIsParticipant( $conversationId, $user->id ) )
            {
                redirect( 'conversations' );
            }

            $this->data['conversationId'] = $conversationId;
            $this->data['conversationData'] = $this->conversationModel->getSingleById( $conversationId );
            $this->data['body'] = '';
            $this->data['bodyError'] = NULL;
            $this->data['addButton'] = false;

            // If the user has made a post request
            if( $_SERVER['REQUEST_METHOD'] == 'POST' )
            {
                if( $this->data['conversationData']->noReply )
                {
                    redirect( 'conversations' );
                }

                // First we Convert this stuff to Markdown
                $_POST['body'] = HTMLToMarkdown( $_POST['body'] );
                // Note that we use filter_var_array here!
                $_POST = filter_var_array( $_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                // Create the data array
                $this->data['body'] = $_POST['body'];

                // The body cannot be empty
                if( empty( trim($this->data['body']) ) )
                {
                    $this->data['bodyError'] = "You response can't be empty.";
                }
                else
                {
                    // Otherwise add the message
                    // The default value for unread in the database is 1
                    // So we don't need to set this
                    $messageData = [
                        'conversationId' => $conversationId,
                        'body' => $this->data['body'],
                        'userId' => $user->id
                    ];
                    if( ! $this->messageModel->createMessage($messageData) )
                    {
                        die("Something went wrong while adding the message!");
                    }

                    flash( 'conversation_message', 'Your message has been sent!') ;
                    redirect( 'conversations/index' );
                }
            }

            $this->data['conversationData']->reported = $this->conversationReportModel->existsByConversationIdAndReportedById( $conversationId, $user->id ) ;

            $messages = $this->messageModel->limit(8)
                                          ->offset(0)
                                          ->orderBy( "createdAt", "DESC" )
                                          ->getByConversationId( $conversationId );
            $messages = array_reverse( $messages );
            // We loaded one extra message, to check if we need to add the "load more messages" button.
            if( count($messages) > 7 )
            {
                $this->data['addButton'] = true;
                array_shift($messages);
            }

            foreach( $messages as &$message )
            {
                if( isset($message->userId) )
                {
                    $sender = $this->userModel->getSingleById( $message->userId, 'name' );
                    $message->name = $sender->name;
                }

                $message->unread = $this->messageModel->isUnreadForUser( $message->id, $user->id );
                $this->messageModel->readMessageForConversation( $message->id, $user->id );
            }

            $this->data['messagesData'] = $messages;
            $this->view( 'conversations/read', $this->data) ;
        }

    }
