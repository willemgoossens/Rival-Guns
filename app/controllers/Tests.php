<?php

  class Tests extends Controller
  {
    public function index()
    {
      $test = false;
      echo isset($test);
      // $testModel = $this->model('Test');
      // $messageModel = $this->model('Message');
      // $conversationModel = $this->model('Conversation');
      //
      // $this->view('tests/index');
      if(isset($_POST))
        var_dump($_POST);
      echo "<form method='post'><input type='text' name='test[0]'><input type='text' name='test[1]'><input type='submit' value='submit'/> </form>";
    }
  }
