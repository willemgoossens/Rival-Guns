<?php
    namespace App\Middleware;
    use App\Libraries\Middleware as Middleware;

    class DebuggingToolbar extends Middleware
    {
        public function __construct(string ...$setup)
        {
            $this->setVariables(...$setup);
        }

        /**
         * 
         * 
         * After
         * @param Object coreClass
         * @return Void
         * 
         * 
         */
        public function after(Object $coreClass): Void
        {
            $ob_content = ob_get_contents();
            ob_clean();
            $toolbar = "<div class='row ml-0 mr-0'><div class = 'debugger'>" . variablePrint($coreClass->returnControllerData()) . "</div></div>";

            $ob_content = preg_replace("/<body(.*?)>/", "<body$1>" . $toolbar, $ob_content);

            echo $ob_content;
        }
    }
