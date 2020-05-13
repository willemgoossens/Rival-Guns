<?php
  /*
   * Base REST_Controller
   * Loads the models and returns responses
   */
  class REST_Controller {
    public $defaultMethod = "index";
    // Load model
    public function model(string $model)
    {
      // Require model file
      require_once '../app/models/' . $model . '.php';

      // Instatiate model
      return new $model();
    }

    public function response(array $data, int $status = 200)
    {
      header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
      // If the response array isn't empty encode the json_array, otherwise dont
      if(!empty($data))
        die(json_encode($data));
      else
        die;
    }

    private function requestStatus($code)
    {
        $status = [
            200 => 'OK',
            400 => 'Bad Request',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
            535 => 'Authentication Failed'
        ];
        // Check if the error code exists in here
        // Otherwise return 500
        return ($status[$code]) ? $status[$code] : $status[500];
    }
  }
