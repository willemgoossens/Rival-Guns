<?php
  class ConversationReports extends Controller
  {
    public function __construct()
    {
      $this->userModel = $this->model('User');
      $this->adminRoleModel = $this->model('AdminRole');
      $this->conversationModel = $this->model('Conversation');
      $this->conversationReportModel = $this->model('ConversationReport');
      $this->punishmentModel = $this->model('punishment');
      $this->messageModel = $this->model('Message');
      $this->notificationModel = $this->model('Notification');

      // Set the sessions for the nav bar
      $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
      $this->data['user']->adminRights         = $this->adminRoleModel->getRightNamesForRole($this->data['user']->adminRole);
      $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
      $this->data['user']->notifications = $this->notificationModel->getUnreadNotifications($_SESSION['userId']);

    }

    /**
     * the index page of all conversation reports
     * @param  integer $page       page number
     * @param  boolean $classified optional whether the page is classified or not
     * @return view
     */
    public function index(int $page = 1, string $classified = NULL)
    {
      $user = &$this->data['user'];
      // If user isn't allow to access page, redirect
      if(! in_array("HandleReportedConversations", $user->adminRights))
      {
        redirect('profile');
      }

      $this->data['totalReports'] = $this->conversationReportModel->countByClassified($classified);
      $this->data['page'] = $page;
      $this->data['reportsPerPage'] = 6;
      $this->data['classified'] = ($classified == 'classified') ? true : false;

      // The page should never be empty
      // So make sure the page nr can't be too high, or 0
      // If the pagenumber is too high, reset to 1
      if($this->data['page'] * $this->data['reportsPerPage'] - $this->data['totalReports'] > $this->data['reportsPerPage'])
      {
        $this->data['page'] = 1;
      }
      // Calculate the offset for the pages to load
      $offset = ($this->data['page'] - 1) * $this->data['reportsPerPage'];
      // Get Conversation Reports
      $this->data['conversationReports'] = $this->conversationReportModel->offset($offset)
                                                                         ->limit($this->data['reportsPerPage'])
                                                                         ->getByClassified($this->data['classified']);

      if(!empty($this->data['conversationReports']))
      {
        foreach($this->data['conversationReports'] as &$report)
        {
          // If classified, get the person who handled the report
          if($report->classified)
          {
            $report->handledBy = $this->userModel->getSingleById($report->handledById);
          }
          // Get conversations and users
          $report->conversation = $this->conversationModel->getSingleById($report->conversationId);
          $report->reportedBy = $this->userModel->getSingleById($report->reportedById);
        }
      }
      $this->view('conversationReports/index', $this->data);
    }

    /**********************************
    *summary
    *
    * CREATE A CONVERSATION REPORT
    * PARAMS: Get -> Conversation ID; POST-> Information about the violations
    *
    **********************************/
    public function create(int $conversationId)
    {
      $user = &$this->data['user'];
      // Redirect if something was wrong with the request
      if($_SERVER['REQUEST_METHOD'] != 'POST'
          || ! $this->conversationModel->checkIfUserIsParticipant($conversationId, $user->id)
          || $this->conversationReportModel->doesReportForConversationAndUserExist($conversationId, $user->id))
      {
        redirect('conversations');
      }
      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
      // Check if there is an explanation given
      if(isset($_POST['otherExplanation']))
      {
        $explanation = $_POST['otherExplanation'];
      }
      else
      {
        $explanation = "";
      }
      // Great! Now we just have to store it in the database
      $conversationReportData = [
        'conversationId' => $conversationId,
        'reportedById' => $_SESSION['userId'],
        'sexismRacism' => isset($_POST['sexismRacism']),
        'spam' => isset($_POST['spam']),
        'insult' => isset($_POST['insult']),
        'other' => isset($_POST['other']),
        'otherExplanation' => $explanation
      ];
      $this->conversationReportModel->insert($conversationReportData);
      // Set the flash message
      flash('conversation_message', '<strong>This conversation has been reported!</strong><br/>We\'ll get back to you ASAP.');
      redirect('conversations/index');
    }

    /**********************************
    *
    *
    * PROCESS A CONVERSATION REPORT
    * @param: punishment params
    * @param: reply to reporter
    * @GET param: report ID
    *
    *
    **********************************/
    public function process(int $id)
    {
      $user = &$this->data['user'];

      // Check if the conversation exists
      $report = $this->conversationReportModel->getSingleByIdAndClassified($id, false);

      // If user isn't allow to access page, redirect
      if(! in_array("HandleReportedConversations", $user->adminRights)
          || !$report)
      {
        redirect('profile');
      }

      $this->data['addButton'] = false;
      $this->data['CDNFiles'] = ['TempusDominus'];
      $this->data['punishments'] = [];
      $this->data['summaryError'] = null;

      $report->conversation = $this->conversationModel->getSingleById($report->conversationId);
      // Get the reporter and the conversation partners
      $report->conversationPartners = $this->conversationModel->getPeopleInConversation($report->conversationId);

      if($_SERVER['REQUEST_METHOD'] == 'POST')
      {
        // Sanitize POST array
        // First we Convert this stuff to Markdown
        $_POST['punishments'] = array_map(function($value)
                                                  {
                                                      $value['justification'] = isset($value['justification'])
                                                      ? HTMLToMarkdown($value['justification']) : null;
                                                      return $value;
                                                  }, $_POST['punishments']);

        $_POST['summary'] = HTMLToMarkdown($_POST['summary']);
        // Now filter out the remainder!
        // Note that we use filter_var_array here!
        $_POST = filter_var_array($_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS, ARRAY_FILTER_USE_BOTH);


        // Since we have an array of possibly indefinite inputs, we will use a variable to store potential errors
        $error = null;

        // Check the input for the conversation partner
        $this->data['punishments'] = $_POST['punishments'];

        $insertArray = [];

        foreach($this->data['punishments'] as $key => &$input)
        {
          // Because the input name includes the ids of the users (for the sake of simplicity)
          // We do need to check whether someone has messed with the ids
          if(!isset($report->conversationPartners[$key]))
          {
            die("No hacking!");
          }

          $input['userId'] = $key;
          $input = $this->punishmentModel->validatePunishmentFormInput($input);

          if(!empty($input['justificationError'])
            || !empty($input['datePickerError']))
          {
            $error = true;
          }
          elseif(!empty($input))
          {
            $insertArray[$key] = [
              "userId" => $key,
              "conversationReportId" => $report->id,
              "endsAt" => $input['datePicker'] ?? null,
              "explanation" => $input['justification'],
              "punishmentType" => $input['punishment'],
              "punishedById" => $user->id
            ];
          }
        }

        $this->data['summary'] = $_POST['summary'];
        if(empty(trim($this->data['summary'])))
        {
          $error = true;

          $this->data['summaryError'] = 'You have not written a summary for the way you handled this report.';
        }
        else
        {
          $this->data['summaryError'] = false;
        }

        if(!isset($error))
        {
          $this->punishmentModel->insertArray($insertArray);

          // At this point we're sure that all punishments were inserted, so send messages now
          foreach($insertArray as $key => $input)
          {
            if($input["punishmentType"] != "permanentBan")
            {
              // restyle temporaryBan
              if($input["punishmentType"] == "temporaryBan")
              {
                $input["punishmentType"] = "temporary ban";
              }

              $insertConversation = [
                "userId" => $user->id,
                "noReply" => true,
                "noReplySender" => SITENAME . " Team",
                "subject" => "Punishment"
              ];

              $message = "Dear " . $report->conversationPartners[$key]->name . ",\r\n\r\nYou have received a " . $input["punishmentType"] . " because of the following reason:\r\n\r\n" . $input['explanation'] . "\r\n\r\nKind regards,\r\n\r\n" . SITENAME . " Team";

              $this->conversationModel->addConversation($insertConversation, $message, [$key]);
            }

          }

          // update the punishment
          $updateArray = [
            'summary' => $this->data['summary'],
            'handledById' => $user->id,
            'classified' => 1
          ];
          if($this->conversationReportModel->updateById($report->id, $updateArray))
          {
            flash('conversationReport_success', 'You have handled and classified the report.');
            redirect('conversationReports');
          }
          else
          {
            die('Something went wrong when classifying the report.');
          }
        } /* END OF If no error exists */

      } /* END OF POST */


      // We need to get the messages, but we need to reverse the order for the view
      $messages = $this->messageModel->offset(0)
                                     ->limit(8)
                                     ->orderBy("createdAt", "DESC")
                                     ->getByConversationId($report->conversationId);
      $messages = array_reverse($messages);
      // We loaded one extra message, to check if we need to add the "load more messages" button.
      if(count($messages) > 7)
      {
        $this->data['addButton'] = true;
        // Remove the first message in the array (the oldest)
        array_shift($messages);
      }
      $report->conversation->messages = $messages;

      $this->data['report'] = $report;
      //die(print_r($this->data));
      $this->view('conversationReports/process', $this->data);
    }

  }
