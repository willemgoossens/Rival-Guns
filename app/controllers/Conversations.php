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

      // Set the sessions for the nav bar
      $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
      $this->data['user']->adminRights         = $this->adminRoleModel->getRightsForInterface($this->data['user']->adminRole);
      $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
    }

    /*
    *
    *
    * index
    * PARAMS: page NR
    *
    *
    */
    public function index($page = 1)
    {
      $user = &$this->data['user'];

      $this->data['conversations'] = '';
      $this->data['totalCases'] = $this->conversationModel->count();
      $this->data['page'] = (int) $page;
      $this->data['casesPerPage'] = '';

      // The page should never be empty
      // So make sure the page nr can't be too high, or 0
      // We put 7 conversations per page
      $this->data['casesPerPage'] = 7;
      // If the pagenumber is too high, reset to 1
      if($this->data['page'] * $this->data['casesPerPage'] - $this->data['totalCases'] > $this->data['casesPerPage'] 
         || $this->data['page'] == 0) 
      {
        $this->data['page'] = 1;
      }
      // Now we calculate the offset that we need when loading the conversations on this specific page
      $offset = ($this->data['page'] - 1) * $this->data['casesPerPage'];

      $this->data['conversations'] = $this->conversationModel
                                          ->getLatestUpdatedConversationsByParticipantId($user->id, $this->data['casesPerPage'], $offset);
      // Now we need to get the information of the conversation partners
      foreach($this->data['conversations'] as &$conversation)
      {
        // Get the last unread message from your conversation partner
        $conversation->unread = $this->conversationModel->getIfConversationHasUnreadMessageForUser($user->id, $conversation->id);
        // Get the last message in this conversation (we need the date)
        $conversation->lastMessage = $this->messageModel
                                          ->limit(1)
                                          ->offset(0)
                                          ->orderBy('createdAt', 'DESC')
                                          ->getSingleByConversationId($conversation->id);
        // We need to set the display name for the last message, if its from another person
        $conversation->lastMessage->senderName = NULL;
        if($conversation->lastMessage->userId != $user->id)
        {
          $sender = $this->userModel->getSingleById($user->id, 'name');
          if(isset($conversation->noReplySender))
          {
            $conversation->lastMessage->senderName = ucfirst($sender->name) . ' on behalf of ' . $conversation->noReplySender;
          }
          else
          {
            $conversation->lastMessage->senderName = 'Last message from ' . ucfirst($sender->name);
          }
        }
      }
      // And show the view
      $this->view('conversations/index', $this->data);
    }

    /*
    *
    *
    * Add conversation
    *
    *
    */
    public function add()
    {
      $user = &$this->data['user'];

      $this->data['body'] = '';
      $this->data['bodyError'] = NULL;
      $this->data['subject'] = '';
      $this->data['subjectError'] = NULL;
      $this->data['to'] = '';
      $this->data['toError'] = NULL;

      if($_SERVER['REQUEST_METHOD'] == 'POST')
      {
        // Sanitize POST array
        // First we Convert this stuff to Markdown
        $_POST['body'] = HTMLToMarkdown($_POST['body']);
        // Now filter out the remainder!
        // Note that we use filter_var_array here!
        $_POST = filter_var_array($_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // Now add the POST data to the $data array
        $this->data['body'] = $_POST['body'];
        $this->data['subject'] = $_POST['subject'];
        $this->data['to'] = $_POST['to'];

        // First validated the username
        if(empty($this->data['to']))
        {
          $this->data['toError'] = "Please enter a username.";
        }
        else
        {
          $toIds = [];
          foreach($this->data['to'] as $to)
          {
            // Check if the user does exist
            $to = $this->userModel->getSingleByName($this->data['to'], 'name', 'id');

            if(empty($to))
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
              array_push($toIds, $to->id);
            }
          }
        }

        // Validate the subject
        if(empty($this->data['subject']))
        {
          $this->data['subjectError'] = "Please enter a subject.";
        }
        else
        {
          $this->data['subjectError'] = false;
        }

        // Validate the body
        if(strlen($_POST['body']) < 12)
        {
          $this->data['bodyError'] = 'Your message has to be at least 12 characters.';
        }
        else
        {
          $this->data['bodyError'] = false;
        }

        if(empty($this->data['bodyError'])
          && empty($this->data['toError'])
          && empty($this->data['subjectError'])
        ) {
          // Create a new conversation
          $insertConversationData = [
            'userId' => $user->id,
            'subject' => $this->data['subject']
          ];

          $this->conversationModel->addConversation($insertConversationData, $this->data['body'], $toIds);

          flash('conversation_message', 'Your message has been send!');
          redirect('conversations');
        }
        else
        {
          // DELETE WHEN ADDING MULTIPLE RECEIVER INPUTS
          $this->data['to'] = $_POST['to'][0];
          $this->data['body'] = $this->data['body'];
        }
      }

      $this->view('conversations/add', $this->data);
    }

    /*
    *
    *
    * Read and Reply on a conversation
    * This function is a regular page visit or a POST request
    * PARAMS: conversation_id, POST-body (optional, in case user sends a reply)
    *
    */
    // Read and reply eply to a conversation
    public function read(int $conversationId)
    {
      $user = &$this->data['user'];
      // Redirect if the conversation_data doesn't exist
      // Or if the user isn't a participant
      if(! $this->conversationModel->checkIfUserIsParticipant($conversationId, $user->id))
      {
        redirect('conversations');
      }
      // Create the data array
      $this->data['conversationId'] = $conversationId;
      $this->data['conversationData'] = $this->conversationModel->getSingleById($conversationId);
      $this->data['body'] = '';
      $this->data['bodyError'] = NULL;
      $this->data['addButton'] = false;

      // If the user has made a post request
      if($_SERVER['REQUEST_METHOD'] == 'POST')
      {
        // if noReply
        if($this->data['conversationData']->noReply)
        {
          redirect('conversations');
        }
        // Sanitize POST array
        // First we Convert this stuff to Markdown
        $_POST['body'] = HTMLToMarkdown($_POST['body']);
        // Now filter out the remainder!
        // Note that we use filter_var_array here!
        $_POST = filter_var_array($_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // Create the data array
        $this->data['body'] = $_POST['body'];

        // The body cannot be empty
        if(empty(trim($this->data['body'])))
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
          if(!$this->messageModel->createMessage($messageData))
          {
            die("Something went wrong while adding the message!");
          }
          // Set the flash message
          flash('conversation_message', 'Your message has been sent!');
          redirect('conversations/index');
        }
      }

      // Check if the user has already reported this conversation
      $this->data['conversationData']->reported = $this->conversationReportModel->existsByConversationIdAndReportedById($conversationId, $user->id);

      // We need to get the messages, but we need to reverse the order for the view
      $messages = $this->messageModel->limit(8)
                                     ->offset(0)
                                     ->orderBy("createdAt", "DESC")
                                     ->getByConversationId($conversationId);
      $messages = array_reverse($messages);

      // We loaded one extra message, to check if we need to add the "load more messages" button.
      if(count($messages) > 7)
      {
        $this->data['addButton'] = true;
        // Remove the first message in the array (the oldest)
        array_shift($messages);
      }

      foreach($messages as &$message)
      {
        $sender = $this->userModel->getSingleById($message->userId, 'name');
        $message->name = $sender->name;

        $message->unread = $this->messageModel->isUnreadForUser($message->id, $user->id);
        // Set as unread
        $this->messageModel->readMessageForConversation($message->id, $user->id);
      }
      // Create the data array
      $this->data['messagesData'] = $messages;

      // Display the view
      $this->view('conversations/read', $this->data);
    }

  }
