<?php

  class DebuggingToolbar extends Middleware
  {
    public function __construct(string ...$setup)
    {
      $this->setVariables(...$setup);
    }

    public function after($coreClass)
    {
      $ob_content = ob_get_contents();
      ob_clean();
      $toolbar = "<div class='row ml-0 mr-0'><div class = 'debugger'>" . variablePrint($coreClass->returnControllerData()) . "</div></div>";

      $ob_content = preg_replace("/<body(.*?)>/", "<body$1>" . $toolbar, $ob_content);

      echo $ob_content;
    }
  }
